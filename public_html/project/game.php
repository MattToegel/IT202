<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    $_SESSION["started"] = new DateTime();
    if(isset($_SESSION["outcome"])){
        unset($_SESSION["outcome"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tanks for Playing</title>
    <meta name="description" content="Tanks for Playing">
    <meta name="author" content="Matt Toegel">
</head>
<body>
<h2>Tanks for Playing</h2>
<!--Your canvas will come here -->
<canvas id="canvas" width="1024" height="512" style="border:1px solid lightgrey;">
    Your browser does not support the HTML5 canvas tag.
</canvas>
<hr/>
<small>Heavily based on this tutorial: <a href="https://spicyyoghurt.com/tutorials/html5-javascript-game-development/develop-a-html5-javascript-game">Here</a></small>
<script>
    "use strict";
    class GameObject{
        constructor (context, x, y, vx, vy, mass,restitution){
            this.context = context;
            this.x = x;
            this.y = y;
            this.vx = vx;
            this.vy = vy;
            this.dx = 0;
            this.dy = 0;
            this.mass = mass;
            this.restitution = restitution;
            this.disabled = false;

            this.isColliding = false;
        }
        getRandomRange(min, max) {
            return Math.random() * (max - min) + min;
        }
        getAngle(x1, x2, y1, y2) {

            // angle in radians
            //var angleRadians = Math.atan2(y2 - y1, x2 - x1);
            //return angleRadians;

            var angleDeg = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
            return angleDeg;

            // angle in degrees
            //var angleDeg = Math.atan2(p2.y - p1.y, p2.x - p1.x) * 180 / Math.PI;
        }
        transitionAngle(fromAngle, toAngle, speed) {
            // normalize the angles to 0-360 range
            const rad360 = 2*Math.PI;
            fromAngle = fromAngle % rad360;
            toAngle = toAngle % rad360;



            if (fromAngle < toAngle) {
                if (Math.abs(fromAngle - toAngle) < Math.PI)
                    fromAngle += speed;
                else fromAngle -= speed;
            }

            else {
                if (Math.abs(fromAngle - toAngle) < Math.PI)
                    fromAngle -= speed;
                else fromAngle += speed;
            }
            return fromAngle;
        }
    }
    class Tank extends GameObject
    {
        constructor (context, x, y, vx, vy, isAI, range = 200, turnSpeed = 1, fireRate = 1){
            //Pass params to super class
            super(context, x, y, vx, vy, 1, 0.9);

            console.log(isAI, x, y, vx, vy);
            //Set default width and height
            this.radius = 25;//mass > 0.5?25:10; //25;
            this.showAngle = true;
            this.angle = 0;
            this.fireRate = fireRate;
            //move direction
            this.dx = 0;
            this.dy = 0;
            //target position
            this.tx = 0;
            this.ty = 0;
            this.range = range*range;
            this.shootArc = .5;
            this.desiredAngle = 0;
            this.bounceOfEdges = false;
            this.turnSpeed = turnSpeed;
            this.up = false;
            this.down = false;
            this.left = false;
            this.right = false;
            this.nextFire = 0;
            this.secondsPassed = 0;
            this.shooting = false;
            this.diameter = this.radius * 2;
            this.halfRadius = this.radius / 2;
            this.totalHealth = 10;
            this.currentHealth = this.totalHealth;
            this.isAI = isAI;
            this.tankColor = "#05652D";
            this.barrelColor = "#034820";
            this.hitColor = "#A2082B";
            this.barrelTipColor = "#023417";
            this.treadColor = "#000000";
            if(this.isAI){
                this.atTarget = true;
            }
            else{

                this.atTarget = false;
            }
            console.log("Am I AI", this.isAI);
        }
        shoot(){
            if(this.secondsPassed >= this.nextFire){
                this.nextFire = this.secondsPassed + this.fireRate;
                console.log("Shoot", this.nextFire);
                this.context.game.spawnBullet(this.x, this.y, this.vx, this.vy, this.angle);
            }
        }
        subDraw(){
            //tank body
            this.context.fillRect(this.x - this.radius, this.y - this.radius, this.diameter, this.diameter);
            //treads
            this.context.fillStyle = this.treadColor;
            this.context.fillRect(this.x - this.radius, this.y - this.radius * 1.1, this.halfRadius, this.diameter * 1.1);
            this.context.fillRect(this.x + this.halfRadius, this.y - this.radius * 1.1, this.halfRadius, this.diameter * 1.1);
            //tank barrel
            this.context.fillStyle = this.barrelColor;
            this.context.fillRect(this.x - (this.radius * .25), this.y-this.diameter, this.halfRadius, this.diameter);
            //tank tip
            this.context.fillStyle = this.barrelTipColor;
            this.context.fillRect(this.x - (this.radius*.25)-2, this.y-this.diameter, this.halfRadius + 4, this.halfRadius);
            this.context.moveTo(this.x, this.y);
            this.context.lineTo(this.x, this.y - 30);
            this.context.stroke();
        }
        lookAtDirection(){
            this.context.beginPath();
            this.context.fillStyle = this.isColliding ? this.hitColor : this.tankColor;
            this.context.translate(this.x, this.y);
            this.context.rotate(Math.PI / 180 * (this.angle + 90)); //increment the angle and rotate the image
            this.context.translate(-this.x, -this.y);
            this.subDraw();
            this.context.fill();
            this.context.setTransform(1, 0, 0, 1, 0, 0);


        }
        drawHealthbar(){
            //background
            this.context.fillStyle = "#000000";
            this.context.fillRect(this.x - this.radius, this.y - this.diameter, this.diameter, this.halfRadius);
            //fill
            this.context.fillStyle = "#ff0000";
            let p = (this.currentHealth / this.totalHealth);
            if(p <= 0){
                p = 0;
            }
            console.log("Health Percent: ", p);
            this.context.fillRect(this.x - this.radius, this.y - this.diameter, this.diameter * p, this.halfRadius );
        }
        draw() {
            //Wrapped drawing logic in function for easy of use and later layering

            this.lookAtDirection();
            this.drawHealthbar();
        }
        takeDamage(dmg){
            this.currentHealth -= dmg;
            if(this.currentHealth <= 0){
                console.log(this,"Died");
                this.disabled = true;
                this.context.game.died(this.isAI);
            }

        }
        doAI(){
            if(this.context.player.disabled){
                this.shooting = false;
                this.left = false;
                this.right = false;
                this.up = false;
                this.down = false;
                return;// We got'em
            }
            if(this.atTarget){
                //residual from other tests, can probably delete
                this.tx = this.context.player.x; //this.getRandomRange(0, this.context.canvas.width);
                this.ty = this.context.player.y;//this.getRandomRange(0, this.context.canvas.height);
                this.atTarget = false;
            }
            //set target x,y to player x,y
            this.tx = this.context.player.x;
            this.ty = this.context.player.y;
            this.desiredAngle = this.getAngle(this.x, this.tx, this.y, this.ty);



            const x2 = Math.pow((this.tx - this.x), 2);
            const y2 = Math.pow((this.ty - this.y), 2);
            const squareDistance = (x2 + y2);
            //convert my angle to 0-360 range
            const p = (this.angle % 360 + 360) % 360;
            //convert desired angle to 0-360 range
            this.desiredAngle = (this.desiredAngle % 360 + 360) % 360;
            //this seems to work well and doesn't flip rotation when it changes from - to + or + to -
            const abs = Math.abs(p - this.desiredAngle);
            if(abs <= this.shootArc){
                this.shooting = true;
            }
            else{
                this.shooting = false;
            }
            //console.log("abs", abs);
            const hp = 180;
            this.left = false;
            this.right = false;
            if(p < this.desiredAngle){
                if(abs < hp){
                    this.right = true;
                }
                else{// if(abs > hp){
                    this.left = true;
                }
            }
            else{// if (p > this.desiredAngle){
                if(abs < hp){
                    this.left = true;
                }
                else{// if(abs > hp){
                    this.right = true;
                }
            }
            if(squareDistance < this.range){
                //console.log("At target");
                this.atTarget = true;
                this.tx = 0;
                this.ty = 0;
                this.up = false;

            }
            else{
                this.up = true;
                //console.log("angles", this.angle, this.desiredAngle);

            }
        }

        update(secondsPassed){
            if(this.disabled){
                return;

            }
            //secondsPassed is a bit of a lie here, it seems to be delta time (tutorial lableed wrong)
            //so we just add it to our local secondsPassed variable
            this.secondsPassed += secondsPassed;
            if(this.shooting){
                this.shoot();
            }
            if(this.isColliding){
                //this.takeDamage(1);
            }
            let g = 9.81 * 20;
            const canvasWidth = this.context.canvas.width;
            const canvasHeight = this.context.canvas.height;
            if(this.isAI){
                this.doAI();
            }
            //deal with rotation
            if(this.left){this.dx = -1;}
            if(this.right){this.dx = 1;}
            if(!this.left && !this.right){this.dx = 0;}

            //heal with movement
            if(this.up && !this.down){this.dy = 1;}
            if(this.down && !this.up){this.dy = -1;}
            if(!this.up && !this.down){this.dy = 0;}

            if(this.dx > 0){
                this.angle += this.turnSpeed * secondsPassed;

            }
            if(this.dx < 0){
                this.angle -= this.turnSpeed * secondsPassed;

            }
            if(this.dy == 0){
                return;//early termination, don't calc move if we aren't moving
            }
            //Move in direction of rotation (lookAt direction)
            let facingx = Math.cos(this.angle * Math.PI / 180);
            let facingy = Math.sin(this.angle * Math.PI / 180);
            //safety check since a previous mechanic in the GameWorld caused NaN values
            if(isNaN(this.dx) || isNaN(this.dy) || isNaN(this.vx) || isNaN(this.vy)){
                return;
            }
            const vx = this.vx * facingx * secondsPassed;
            const vy = this.vy * facingy * secondsPassed;
            //apply the move if we're meant to move
            if (this.dy > 0) {
                this.x += vx;
                this.y += vy;
            }
            else if (this.dy < 0) {
                this.x -= vx;
                this.y -= vy;
            }
        }
    }
    class Circle extends GameObject
    {
        constructor (context, x, y, vx, vy, radius, showAngle, bounceOfEdges){
            //Pass params to super class
            super(context, x, y, vx, vy, 1, 0.9);

            //Set default width and height
            this.radius = radius;
            this.showAngle = true;
            this.bounceOfEdges = false;
            this.angle = 0;
        }

        draw() {
            if(this.disabled){
                return;
            }
            //Draw a simple square
            this.context.fillStyle = this.isColliding ? '#ff8080' : '#0099b0';

            this.context.beginPath();
            this.context.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
            this.context.fill();

            if (this.showAngle) {
                this.frameCenterX = (this.x - this.radius) + (this.radius * 2) / 2; //(this.column * this.frameWidth) + this.frameWidth / 2;
                this.frameCenterY = (this.y - this.radius) + (this.radius * 2) / 2; //this.frameHeight / 2; //(this.row * this.frameHeight) + this.frameHeight / 2;

                //this.context.save();
                this.context.translate(this.frameCenterX, this.frameCenterY); //let's translate
                this.context.rotate(Math.PI / 180 * (this.angle + 90)); //increment the angle and rotate the image
                this.context.translate(-this.frameCenterX, -this.frameCenterY);

                this.context.beginPath();
                this.context.moveTo(this.x, this.y);
                this.context.lineTo(this.x, this.y - 30);
                this.context.stroke();

                // Reset transformation matrix to the identity matrix
                this.context.setTransform(1, 0, 0, 1, 0, 0);
                // this.context.restore();
            }
        }


        update(secondsPassed){
            if(this.disabled){
                return;
            }
            let g = 9.81 * 20;
            const canvasWidth = this.context.canvas.width;
            const canvasHeight = this.context.canvas.height;

            if (this.bounceOfEdges){
                // Apply g acceleration
                this.vy += g * secondsPassed;
            }

            //Move with velocity x/y
            this.x += this.vx * secondsPassed;
            this.y += this.vy * secondsPassed;

            if (this.showAngle){
                let angleRadians = Math.atan2(this.vy, this.vx); //in radians
                let degrees = 180*angleRadians/Math.PI; // to degrees
                this.angle =  degrees; //(360+Math.round(degrees))%360;
            }
        }
    }
    class Bullet extends Circle{
        //constructor (context, x, y, vx, vy, radius, showAngle, bounceOfEdges){
        constructor (context, x, y, vx, vy, radius, type=3){
            if(type == 3){
                radius *= .5;
            }
            //Pass params to super class
            super(context, x, y, vx, vy, radius, false, false);
            this.damage = 1;
            this.type = type
            this.halfRadius = this.radius * .5;
            this.diameter = this.radius * 2;
        }
        subDraw(){
            if(this.type == 1){
                this.context.beginPath();
                this.context.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
                this.context.fill();
            }
            else if(this.type == 2){
                this.context.beginPath();
                this.context.arc(this.x, this.y, this.radius, Math.PI, 0);
                this.context.fillRect(this.x-this.radius, this.y+this.halfRadius, this.diameter, this.diameter);
                this.context.fill();
            }
            else if(this.type==3){
                this.context.beginPath();
                this.context.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
                this.context.fill();
            }
        }
        lookAtDirection(){
            this.context.beginPath();
            this.context.fillStyle = this.isColliding ? '#000000' : '#404040';
            this.context.translate(this.x, this.y);
            this.context.rotate(Math.PI / 180 * (this.angle + 90)); //increment the angle and rotate the image
            this.context.translate(-this.x, -this.y);
            this.subDraw();
            this.context.fill();
            this.context.setTransform(1, 0, 0, 1, 0, 0);

        }
        draw() {
            if(this.disabled){
                return;
            }
            this.lookAtDirection();
        }
    }

    class Square extends GameObject
    {
        constructor (context, x, y, vx, vy, mass){
            //Pass params to super class
            super(context, x, y, vx, vy, mass, 1);

            //Set default width and height
            this.width = 50;
            this.height = 50;
        }

        draw(){
            //Draw a simple square
            this.context.fillStyle = this.isColliding?'#ff8080':'#0099b0';
            this.context.fillRect(this.x, this.y, this.width, this.height);
        }

        update(secondsPassed){

            /*if (this.gravityAndMass){
                this.vy += (9.81 * 3) * secondsPassed;
            }*/

            //Move with velocity x/y
            this.x += this.vx * secondsPassed;
            this.y += this.vy * secondsPassed;
        }
    }
    const Keys = {
        W : 87,
        S : 83,
        A : 65,
        D : 68,
        SPACE : 32,
        UP : 38,
        DOWN : 40,
        LEFT : 37,
        RIGHT : 39
    };
    class GameWorld {
// Trigger init function when the page has loaded

        constructor(showCollision, showCircles, bounce, gravityAndMass, showAngle, bounceOfEdges) {
            this.canvas = null;
            this.context = null;
            this.oldTimeStamp = 0;
            this.gameObjects = [];
            this.resetCounter = 0;
            this.showCollision = showCollision;
            this.showCircles = showCircles;
            this.bounce = bounce;
            this.gravityAndMass = gravityAndMass;
            this.showAngle = showAngle;
            this.bounceOfEdges = bounceOfEdges;
            this.gameOver = false;
            //this.contxt. player = null;
        }
        listen(){
            if(this.context.player != null){
                let self = this;
                window.addEventListener("keyup", function(e){
                    if ( [Keys.W, Keys.UP].includes(e.keyCode)) {
                        self.context.player.up = false;
                    }

                    if ( [Keys.S, Keys.DOWN].includes(e.keyCode) ) {
                        self.context.player.down = false;
                    }

                    if ( [Keys.A, Keys.LEFT].includes(e.keyCode) ) {
                        //self.context.player.dx = 0;
                        self.context.player.left = false;
                    }

                    if ( [Keys.D, Keys.RIGHT].includes(e.keyCode) ) {
                        //self.context.player.dx = 0;
                        self.context.player.right = false;
                    }

                    if(e.keyCode == Keys.SPACE){
                        self.context.player.shooting = false;
                    }
                });
                var down = function(e){
                    if ( [Keys.W, Keys.UP].includes(e.keyCode) ) {
                        self.context.player.up = true;
                    }

                    if ( [Keys.S, Keys.DOWN].includes(e.keyCode)) {
                        self.context.player.down = true;
                    }

                    if ( [Keys.A, Keys.LEFT].includes(e.keyCode) ) {
                        //self.context.player.dx = -1;
                        self.context.player.left = true;
                    }

                    if ( [Keys.D, Keys.RIGHT].includes(e.keyCode) ) {
                        //self.context.player.dx = 1;
                        self.context.player.right = true;
                    }

                    if(e.keyCode == Keys.SPACE){
                        self.context.player.shooting = true;
                    }
                };

                window.addEventListener("keydown", down);
                window.addEventListener("keypress", down);
            }
        }
        init(canvasId) {
            this.canvas = document.getElementById(canvasId);
            this.context = this.canvas.getContext('2d');

            this.createWorld();

            // Request an animation frame for the first time
            // The gameLoop() function will be called as a callback of this request

            window.requestAnimationFrame((timeStamp) => this.gameLoop(timeStamp));
        }

        createWorld() {
            this.context.game = this;
            this.gameObjects = [
                new Tank(this.context, 250, 50, 200, 200, false, 200, 100),
                new Tank(this.context, 150, 50, 200, 200, true, 200, 50),
                //new Tank(this.context, 250, 300, 0, -50, 1, this.showAngle, this.bounceOfEdges),
                /*new Circle(this.context, 200, 0, 50, 50),*/
                //new Tank(this.context, 150, 0, 50, 50, 1, this.showAngle, this.bounceOfEdges),
                //new Tank(this.context, 250, 150, 50, 50, 1, this.showAngle, this.bounceOfEdges),
                new Bullet(this.context, 300, 75, 50, 50, 5)
                /*new Circle(this.context, 300, 75, -50, 50),*/
                //new Tank(this.context, 350, 75, -50, 50, 1, this.showAngle, this.bounceOfEdges),
                //new Tank(this.context, 300, 300, 50, -50, 1, this.showAngle, this.bounceOfEdges)
            ];

            this.context.player = this.gameObjects[0];
            this.listen();
        }
        died(isAI){
            if(!this.gameOver){
                saveScore(!isAI?"win":"loss");
            }
        }
        spawnBullet(x, y, vx, vy, angle){
            let bullet;
            for(let i = 0; i < this.gameObjects.length; i++){
                let g = this.gameObjects[i];
                if(g instanceof Bullet){
                    if(g.disabled){
                        bullet = g;
                        break;
                    }
                }
            }
            if(!bullet){
                bullet = new Bullet(this.context, 300, 75, 50, 50, 5);
                this.gameObjects.push(bullet);
            }
            bullet.disabled = false;
            //const vx = x - tx;
            //const vy = y - ty;
            const vectorX = Math.cos(angle * Math.PI / 180);
            const vectorY = Math.sin(angle * Math.PI / 180);
            const _x = x + vectorX * 30 * 1.5;
            const _y = y + vectorY * 30 * 1.5;
            bullet.x = _x;
            bullet.y = _y;
            bullet.vx = vectorX * (150+vx);
            bullet.vy = vectorY * (150+vy);
        }

        gameLoop(timeStamp) {

            // Calculate how much time has passed
            let secondsPassed = (timeStamp - this.oldTimeStamp) / 1000;
            this.oldTimeStamp = timeStamp;

            secondsPassed = Math.min(secondsPassed, 0.1);
            /*this.resetCounter += secondsPassed;
            if (this.resetCounter > (this.bounceOfEdges ? 15 : 5)) {
                this.resetCounter = 0;
                this.createWorld();
            }*/

            for (let i = 0; i < this.gameObjects.length; i++) {
                this.gameObjects[i].update(secondsPassed);
            }

            if (this.showCollision) {
                this.detectCollisions(secondsPassed);
            }

            this.clearCanvas();

            for (let i = 0; i < this.gameObjects.length; i++) {
                this.gameObjects[i].draw();
            }
            // The loop function has reached it's end
            // Keep requesting new frames
            window.requestAnimationFrame((timeStamp) => this.gameLoop(timeStamp));
        }
        destroyPlayer(obj){
            if(obj instanceof Tank && !obj.isAI){
                obj.takeDamage(obj.totalHealth);
            }
        }
        detectCollisions(secondsPassed) {
            var obj1;
            var obj2;
            for (var i = 0; i < this.gameObjects.length; i++) {
                obj1 = this.gameObjects[i];
                obj1.isColliding = false;
                if(obj1.disabled){
                    continue;
                }
                //obj1.oldVx = obj1.vx;
                //obj1.oldVy = obj1.vy;
                if(true){
                    const canvasWidth = this.context.canvas.width;
                    const canvasHeight = this.context.canvas.height;
                    if (obj1.x < obj1.radius) {
                        //obj1.vx = Math.abs(obj1.vx) * 0.9;
                        obj1.x = obj1.radius;
                        obj1.isColliding = true;
                    } else if (obj1.x > canvasWidth - obj1.radius) {
                        //obj1.vx = -Math.abs(obj1.vx) * 0.90;
                        obj1.x = canvasWidth - obj1.radius;
                        obj1.isColliding = true;
                    }
                    if (obj1.y < obj1.radius) {
                        //obj1.vy = Math.abs(obj1.vy) * 0.90;
                        obj1.y = obj1.radius;
                        obj1.isColliding = true;
                    } else if (obj1.y > canvasHeight - obj1.radius) {
                        //obj1.vy = -Math.abs(obj1.vy) * 0.90;
                        obj1.y = canvasHeight - obj1.radius;
                        obj1.isColliding = true;
                    }
                    if(obj1 instanceof Bullet && obj1.isColliding){
                        obj1.disabled = true;
                    }
                }
            }

            for (var i = 0; i < this.gameObjects.length; i++) {
                obj1 = this.gameObjects[i];
                if(obj1.disabled){
                    continue;
                }
                for (var j = i + 1; j < this.gameObjects.length; j++) {
                    obj2 = this.gameObjects[j];
                    if(obj2.disabled){
                        continue;
                    }
                    let collided = false;
                    //don't really care how they collided, just check
                    if (this.showCircles) {
                        if (this.circleIntersect(obj1.x, obj1.y, obj1.radius, obj2.x, obj2.y, obj2.radius)) {
                            collided = true;
                        }
                    } else {
                        if (this.rectIntersect(obj1.x, obj1.y, obj1.width, obj1.height, obj2.x, obj2.y, obj2.width, obj2.height)) {
                            collided = true;
                        }
                    }
                    //ok lets to the logic
                    if(collided){
                        obj1.isColliding = true;
                        obj2.isColliding = true;
                        //TODO check if both are bullets? - Think I'll keep it as an interesting game mechanic
                        if(obj1 instanceof Bullet){
                            obj1.disabled = true;
                            if(obj2 instanceof Tank){
                                obj2.takeDamage(obj1.damage);
                            }
                        }
                        if(obj2 instanceof Bullet){
                            obj2.disabled = true;
                            if(obj1 instanceof Tank){
                                obj1.takeDamage(obj2.damage);
                            }
                        }
                        if(obj1 instanceof Tank && obj2 instanceof Tank){
                            //if one of these is a player it'll be destroyed
                            this.destroyPlayer(obj1);
                            this.destroyPlayer(obj2);
                        }
                    }
                }
            }
        }
        getDistance(x1, y1, x2, y2){
            return Math.sqrt((x2-x1)*(x2-x1) + (y2-y1)*(y2-y1));
        }


        circleIntersect(x1, y1, r1, x2, y2, r2) {

            // Calculate the distance between the two circles
            //var distance = (x1-x2)*(x1-x2) + (y1-y2)*(y1-y2);
            var distance = (x2-x1)*(x2-x1) + (y2-y1)*(y2-y1);

            // When the distance is smaller or equal to the sum
            // of the two radius, the circles overlap
            return distance <= ((r1 + r2) * (r1 + r2))
        }

        rectIntersect(x1, y1, w1, h1, x2, y2, w2, h2) {

            // Check x and y for overlap
            if (x2 > w1 + x1 || x1 > w2 + x2 || y2 > h1 + y1 || y1 > h2 + y2){
                return false;
            }

            return true;
        }

        clearCanvas() {
            // Clear the canvas
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
    }

    function init(canvasId, showCollision, showCircles, bounce, gravityAndMass, showAngle, bounceOfEdges){
        console.log("called");
        var gameWorld = new GameWorld(showCollision, showCircles, bounce, gravityAndMass, showAngle, bounceOfEdges);
        gameWorld.init(canvasId);
        console.log("init world");
    }
</script>
<script>

    function getGameData(){
        var xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            if (xhttp.status != 200) { // analyze HTTP status of the response
                alert(`Error ${xhttp.status}: ${xhttp.statusText}`); // e.g. 404: Not Found
            } else { // show the result
                alert(`Done, got ${xhttp.response.length} bytes`); // response is the server
            }
        };
        xhttp.onerror = function() {
            alert("Request failed");
        };
        xhttp.open("GET", "home.php", true);
        xhttp.send();
    }
    function saveScore(gameState){
        var xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            if (xhttp.status != 200) { // analyze HTTP status of the response
                console.log(`Error ${xhttp.status}: ${xhttp.statusText}`); // e.g. 404: Not Found
                alert("Something went wrong, the activity of this play may have been lost");
            } else { // show the result
                console.log(`Done, got ${xhttp.response.length} bytes`); // response is the server
                window.location.replace(window.location.origin +"/outcome.php");
            }
        };
        xhttp.onerror = function() {
            alert("Request failed");
        };
        xhttp.open("POST", "/api/save_score.php", true);
        xhttp.send("score=1&outcome=" + gameState);
    }
    window.onload = function(){
        //ajax call to get game data from server
        //fetch player tank
        //fetch enemy tank
        //canvasId, showCollision, showCircles, bounce, gravityAndMass, showAngle, bounceOfEdges
        init('canvas', true, true, true, false, true, true);
        console.log("init");
    };


</script>
</body>
</html>