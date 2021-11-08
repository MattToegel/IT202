<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<div class="container-fluid">
    <h1>Ducks Be Gone</h1>
    <canvas tabindex="1" height="400px"></canvas>
</div>
<!-- Need to load an image resource to use it on the Canvas -->
<img src="duck.png" style="display: none;" />
<script>
    //https://spicyyoghurt.com/tools/easing-functions
    var canvas = document.getElementsByTagName("canvas")[0];
    var context = canvas.getContext("2d");
    let img = document.getElementsByTagName("img")[0];
    //mouse position
    let mp = {
        x: 0,
        y: 0
    };
    //start position of launcher handle
    let start = {
        x: 0,
        y: 0
    };
    //launcher grip/holder
    let grip = {
        x: 0,
        y: 0,
        dx: 0,
        dy: 0,
        s: 1000,
        power: 0,
        didTrigger: false
    };

    let secondsPassed = 0;
    //cached img half and quarter dimensions (division is expensive in general)
    let imgDimensions = {
        WQ: 0,
        HQ: 0,
        WH: 0,
        HH: 0
    }

    let duckData = {
        bounceModifier: 1.05,
        maxDucks: 20,
        spawnInterval: 1000
    }

    let gameData = {
        score: 0,
        maxTime: 60,
        timeRemaining: 60,
        allowPiercingShots: true,
        bouncyProjectiles: true,
        isPlaying: false,
        oldTimeStamp: 0,
        maxDist: 8500,
        projectiles: [],
        ducks: []
    }
    let fps;
    //position/dimensions for start button (on start screen)
    const startButton = {
        x: canvas.width * .3,
        y: canvas.height * .3,
        w: canvas.width * .4,
        h: canvas.height * .1,
    }
    //defines a duck object
    const makeDuck = (x, y, r, s) => {
        return {
            x: x,
            y: y,
            dx: (Math.random() > .5 ? 1 : -1),
            dy: 0,
            r: r,
            s: s,
            hit: false,
            lifetime: 0,
            thinker: function() {
                if (Math.random() > .5) {
                    if (!this.dx) {
                        this.dx = (Math.random() > .5 ? 1 : -1)
                    }
                    this.dx *= -1;
                }
                if (!this.hit) {
                    let t = Math.max(500, Math.random() * 3000);
                    //Important: can't directly pass this to setTimeout
                    //after an iteration "this" becomes window and not the object
                    //so we need to pass "this" into a function for it to keep working
                    setTimeout(() => {
                        this.thinker();
                    }, t);
                }
            },
            setHit: function() {
                this.hit = true;
            },
            draw: function() {
                if (this.hit) {
                    return;
                }

                //draw the hit box (for debugging)
                /*
                context.beginPath();
                context.fillStyle = "yellow";
                context.arc(this.x, this.y, this.r, 0, 360);
                context.fill();
                context.closePath();
                */

                context.save();
                let d = this.dx * -1;
                context.imageSmoothingEnabled = true;
                context.imageSmoothingQuality = 'high';
                //canvas wizardry to get the image to rotate or flip (scale in negative axis)
                //see "rotating images on canvas" https://spicyyoghurt.com/tutorials/html5-javascript-game-development/images-and-sprite-animations
                context.translate(this.x, this.y);
                context.scale(d, 1);
                context.translate(-this.x, -this.y);
                //do the draw
                context.drawImage(img, this.x - imgDimensions.WQ, this.y - imgDimensions.HQ, imgDimensions.WH, imgDimensions.HH);
                context.restore();
            },
            move: function(secondsPassed) {
                if (this.hit) {
                    return;
                }
                this.x += this.s * this.dx * secondsPassed;
                this.y += this.s * this.dy * secondsPassed;
                //increase the speed on bounce and invert x
                if (this.x < 0) {
                    this.x = 0;
                    this.s *= duckData.bounceModifier;
                    this.dx *= -1;
                }
                if (this.x + this.r > canvas.width) {
                    this.x = canvas.width - this.r;
                    this.s *= duckData.bounceModifier;
                    this.dx *= -1;

                }
            }
        }
    }
    // defines a projectile
    const makeProjectile = (x, y, r, s, c) => {
        return {
            x: x,
            y: y,
            dx: 0,
            dy: 0,
            r: r,
            r2: r / 2,
            s: s,
            ss: s,
            c: c,
            released: false,
            hit: false,
            lifetime: 0,
            launchFrom: function(start, target, power) {
                this.x = start.x + this.r / 2;
                this.y = start.y;
                this.dx = start.x - target.x;
                this.dy = start.y - target.y;
                this.released = true;
                this.s = power * this.s;
            },
            draw: function() {
                if (this.hit) {
                    return;
                }
                context.beginPath();
                context.fillStyle = this.c;
                context.arc(this.x - this.r2, this.y - this.r2, this.r, 0, 360);
                context.fill();
                context.closePath();

            },
            move: function(secondsPassed) {
                if (this.hit) {
                    return;
                }
                if (!this.released) {
                    this.x = mp.x; //+this.r/2;
                    this.y = mp.y; //+this.r/2;
                } else {
                    if (gameData.bouncyProjectiles) {
                        if (this.x < 0 || this.x + this.r > canvas.width) {
                            this.dx *= -1;
                        }
                    }
                    this.x += this.s * this.dx * secondsPassed;
                    this.y += this.s * this.dy * secondsPassed;
                    this.lifetime += secondsPassed;
                    //movement decay (like drag or wind resistance)
                    if (this.lifetime.toFixed(2) % .25 == 0) {
                        if (this.s > 0) {
                            this.s -= this.ss * .075;
                        } else if (this.s <= 0) {
                            this.s = 0;
                            this.hit = true;
                        }
                    }
                }
            }
        }
    }

    const distance = (x1, y1, x2, y2) => {
        return (x2 - x1) * (x2 - x1) + (y2 - y1) * (y2 - y1);
    }
    const intersect = (x1, y1, r1, x2, y2, r2) => {
        return distance(x1, y1, x2, y2) <= ((r1 + r2) * (r1 + r2));
    }

    const release = (e) => {
        if (grip.didTrigger) {
            grip.didTrigger = false;
            //used to calculate power (speed) and start point of shot
            grip.releasedFrom = {
                x: grip.x,
                y: grip.y
            };
            grip.release = distance(grip.x, grip.y, start.x, start.y);
            console.log("release", grip.release);
        }
    }
    const duckSpawner = setInterval(() => {
        //only draw ducks if we're playing and ducks aren't at max

        if (!gameData.isPlaying || gameData.ducks.length >= duckData.maxDucks) {
            return;
        }
        //random x between 0 and canvas width
        let x = Math.random() * canvas.width;
        //random start height between 10% of canvas height and 30% canvas height (10% + 20%)
        let y = (canvas.height * .1 + (Math.random() * canvas.height * .2));
        //random speed between 1 and 11
        let s = 1 + (Math.random() + 10);
        let d = makeDuck(x, y, 10, s);
        d.thinker();
        gameData.ducks.push(d);
    }, duckData.spawnInterval);

    const timeCountdown = setInterval(() => {
        if (gameData.isPlaying && gameData.timeRemaining > 0) {
            gameData.timeRemaining--;
        } else if (gameData.isPlaying && gameData.timeRemaining <= 0) {
            gameOver();
            gameData.timeRemaining = 0;
            gameData.isPlaying = false;

        }
    }, 1000);
    const gameOver = () => {
        if (gameData.score > 0 && gameData.timeRemaining <= 0) {
            //TODO save examples
            let example = 2;

            if (example === 1) {
                //original way
                let http = new XMLHttpRequest();
                http.onreadystatechange = () => {
                    if (http.readyState == 4 && http.status == 200) {
                        let data = JSON.parse(http.responseText);
                        console.log("received data", data);
                        console.log("Saved score");
                    }
                }
                http.open("POST", "api/save_score.php", true);
                http.send(`score=${gameData.score}`);
            } else if (example === 2) {
                //fetch api way
                fetch("api/save_score.php", {
                    method: "POST",
                    headers: {
                        "Content-type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({
                        score: gameData.score,
                        //TODO pass game state for validation (anti-cheating)
                    })
                }).then(async res => {
                    let data = await res.json();
                    console.log("received data", data);
                    console.log("saved score");
                })
            } else if (example === 3) {
                //TBD jQuery way
            }
        }
    };
    window.addEventListener("load", () => {
        start = Object.freeze({
            x: canvas.width / 2, //center
            y: canvas.height * .7 //70% of canvas
        })
        grip.x = start.x;
        grip.y = start.y;
        //need to set these here since it takes time for the image to load
        imgDimensions.WQ = img.width / 4;
        imgDimensions.Q = img.height / 4;
        imgDimensions.WH = img.width / 2;
        imgDimensions.HH = img.height / 2;
        //start the game loop
        window.requestAnimationFrame(gameLoop);
    });
    canvas.addEventListener("mousemove", (e) => {
        //console.log(e);
        scaledMP(e);
        if (!grip.didTrigger) {
            //ignore if the last item in queue has been released
            if (gameData.projectiles.length && !gameData.projectiles[gameData.projectiles.length - 1].released) {
                return;
            }
            //if the mouse intersected with the handle start the "pull" for the launch
            if (intersect(mp.x, mp.y, 5, start.x, start.y, 10)) {
                grip.didTrigger = true;
                let p = makeProjectile(grip.x + 10, grip.y, 10, 5, "blue");
                gameData.projectiles.push(p);
            }
        }
    });
    const scaledMP = (e) => {
        //scaled mouse position on canvas https://stackoverflow.com/a/17130415
        var rect = canvas.getBoundingClientRect(); // abs. size of element
        scaleX = canvas.width / rect.width; // relationship vs. element for X
        scaleY = canvas.height / rect.height; // relationship vs. element for Y

        mp.x = (e.clientX - rect.left) * scaleX;
        mp.y = (e.clientY - rect.top) * scaleY;

    }
    window.addEventListener("mouseleave", release);
    window.addEventListener("mouseup", release);
    window.addEventListener("mousedown", (e) => {
        //handles start button click
        if (!gameData.isPlaying) {
            scaledMP(e);
            if (mp.x >= startButton.x && mp.x <= startButton.x + startButton.w &&
                mp.y >= startButton.y && mp.y <= startButton.y + startButton.h) {
                resetGame();
                gameData.isPlaying = true;
            }
        }
    });
    const resetGame = () => {
        gameData.timeRemaining = gameData.maxTime;
        gameData.score = 0;
        gameData.ducks = [];
        gameData.projectiles = [];
        grip.didTrigger = false;
    }
    const calcFPS = () => {
        fps = Math.round(1 / secondsPassed);
    }
    const drawFPS = () => {
        // Draw number to the screen
        context.font = '18px Arial';
        context.fillStyle = 'white'; //assumes dark background
        context.fillText("FPS: " + fps, canvas.width * .05, canvas.height * .075);
    }
    const gameLoop = (timeStamp) => {
        //alternative way of clearing the scene
        context.clearRect(0, 0, canvas.width, canvas.height);
        // Calculate the number of seconds passed since the last frame
        // without this the game becomes FPS bound (faster FPS faster movement and vice versa)
        secondsPassed = (timeStamp - gameData.oldTimeStamp) / 1000;
        secondsPassed = Math.min(secondsPassed, 0.1);
        gameData.oldTimeStamp = timeStamp;

        // Calculate fps
        calcFPS();

        // Pass the time to the update
        update(secondsPassed);

        // Perform the drawing operation
        draw();
        // The loop function has reached it's end. Keep requesting new frames
        window.requestAnimationFrame(gameLoop);
    }
    const drawUI = () => {
        context.textAlign = "left";
        //draw background color
        context.fillStyle = "black";
        context.fillRect(0, 0, canvas.width, canvas.height * .1);
        //draw header texts
        drawFPS();
        context.fillText(`Score: ${gameData.score}`, canvas.width * .75, canvas.height * .075)
        context.fillText(`Time: ${gameData.timeRemaining}`, canvas.width * .4, canvas.height * .075);
        if (!gameData.isPlaying) {
            context.strokeStyle = "black";
            //start screen
            context.fillStyle = "black";
            context.textAlign = "center";
            context.fillText("Ducks Be Gone", canvas.width * .5, canvas.height * .25);
            context.fillText("Start", canvas.width * .5, canvas.height * .36);
            context.strokeRect(startButton.x, startButton.y, startButton.w, startButton.h);
        }
    }
    const draw = () => {
        if (gameData.isPlaying) {
            context.beginPath();
            context.fillStyle = 'black';

            if (grip.power > 0) {

                if (grip.power <= .25) {
                    context.strokeStyle = "green";
                } else if (grip.power <= .5) {
                    context.strokeStyle = "yellow";
                } else if (grip.power <= .75) {
                    context.strokeStyle = "orange";
                } else {
                    context.strokeStyle = "red";
                }
            } else {
                context.strokeStyle = "black";
            }
            //draw launcher and anchors
            //left anchor
            context.moveTo(canvas.width * .1, canvas.height * .7);
            context.lineTo(grip.x, grip.y);
            //right anchor
            context.moveTo(canvas.width * .9, canvas.height * .7);
            context.lineTo(grip.x, grip.y);
            context.stroke();
            context.fillStyle = 'black';
            //handle
            context.beginPath();
            context.arc(grip.x, grip.y, 10, 0, 2 * Math.PI);
            context.fill();
            context.stroke();
            context.closePath();
            //draw ducks
            for (let d of gameData.ducks) {
                d.draw();
            }
            //draw projectiles
            for (let c of gameData.projectiles) {
                c.draw();
            }
        }
        //drawing UI last so it's on top of everything
        drawUI();

    }

    const update = (secondsPassed) => {
        if (!gameData.isPlaying) {
            return;
        }
        //follow mouse if aiming
        if (grip.didTrigger) {
            //magic value 5 for cursor dimensions
            grip.x = mp.x - 5;
            grip.y = mp.y - 5;
            let d = distance(grip.x, grip.y, start.x, start.y);
            grip.power = Math.min(d / gameData.maxDist, 1);
        } else {
            //move holder back to start position
            if (distance(grip.x, grip.y, start.x, start.y) < .1) {
                grip.dx = (start.x - grip.x) / start.x;
                grip.dy = (start.y - grip.y) / start.y;

                console.log(grip.dx, grip.dy);
                grip.x += grip.s * grip.dx * secondsPassed;
                grip.y += grip.s * grip.dy * secondsPassed;

                /*let dist = distance(grip.x, grip.y, start.x, start.y)
                //console.log(dist, start);
                if(dist < 10 || (isNaN(grip.x) || isNaN(grip.y))){
                    console.log("reset");
                    grip.x = start.x;
                    grip.y = start.y;
                    grip.release = 0;
                }*/
            } else {
                grip.x = start.x;
                grip.y = start.y;
                //fire shot if at start position and there's an unreleased projectile
                if (gameData.projectiles.length && !gameData.projectiles[gameData.projectiles.length - 1].released) {
                    //TODO display power
                    grip.power = Math.min(grip.release / gameData.maxDist, 1);
                    console.log("launched with power", grip.power);
                    gameData.projectiles[gameData.projectiles.length - 1].launchFrom(start, grip.releasedFrom, grip.power);

                    grip.power = 0;
                }

            }
        }

        for (let d of gameData.ducks) {
            d.move(secondsPassed);
        }

        for (let c of gameData.projectiles) {
            c.move(secondsPassed);
        }
        //remove hit or out of bounds shots
        gameData.projectiles = gameData.projectiles.filter((i) => i.y > 0 && !i.hit);
        for (let c of gameData.projectiles) {
            if (c.hit) {
                continue;
            }
            for (let d of gameData.ducks) {
                if (!d.hit && !c.hit && intersect(c.x, c.y, c.r, d.x, d.y, d.r)) {
                    console.log("hit", c, d);
                    d.setHit();
                    gameData.score += 10;
                    if (!gameData.allowPiercingShots) {
                        c.hit = true;
                    }

                }
            }
        }
        //remove hit ducks
        gameData.ducks = gameData.ducks.filter((i) => !i.hit);
    }
</script>
<style>
    body {
        overflow: hidden;
    }

    canvas {
        width: 80%;
        max-height: 80vh;
        display: block;
        border: 1px solid black;
        margin-left: auto;
        margin-right: auto;

        left: 0;
        bottom: 0;
        right: 0;
    }
</style>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>