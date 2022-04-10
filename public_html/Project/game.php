<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<div style="height: 70%; width: 85%;" class="container-fluid">
    <div class="h1">Rescue Mission</div>
    <div id="stats" class="row">
        <div class="col">
            <span id="level" class="lead">Level: 1</span>
        </div>
        <div class="col"><span id="score" class="lead">Score: 0</span></div>
    </div>
    <div class="row g-4 h-100">
        <div class="col">
            <canvas id="board" width="1024px" height="1024px" style="aspect-ratio:1; width:auto; height:100%;">
            </canvas>
        </div>
        <div class="col-3" style="min-width: 300px">
            <div class="row">
                <table class="table">
                    <tr>
                        <td></td>
                        <td style="text-align:center"><button class="btn btn-secondary" onClick="move(0,-1)">Up</button></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align:right"><button class="btn btn-secondary" onClick="move(-1,0)">Left</button></td>
                        <td></td>
                        <td style="text-align:left"><button class="btn btn-secondary" onClick="move(1,0)">Right</button></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align:center"><button class="btn btn-secondary" onClick="move(0,1)">Down</button></td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="row">
                <span id="hints" style="text-align:center"></span>
            </div>
            <div class="row">
                <?php require_once(__DIR__ . "/../../partials/inventory.php"); ?>
            </div>
        </div>
    </div>
    <div style="display:none">
        <img src="<?php echo get_url("static/images/dog.png") ?>" id="wolf" />
        <img src="<?php echo get_url("static/images/chick.png") ?>" id="player" />
        <img src="<?php echo get_url("static/images/chicken.png") ?>" id="f1" />
        <img src="<?php echo get_url("static/images/duck.png") ?>" id="f2" />
        <img src="<?php echo get_url("static/images/owl.png") ?>" id="f3" />
    </div>
</div>
<script>
    const canvas = document.getElementById("board");
    const ctx = canvas.getContext("2d");
    let w = canvas.width;
    let h = canvas.height;
    console.log("w/h", w, h);
    let gridRows = 3;
    let gridColumns = 3;
    let rw = 0; //Math.ceil(w/gridColumns);
    let rh = 0; //Math.ceil(h/gridRows);
    const CellTypes = {
        Wolf: "W",
        Ladder: "L",
        Pit: "P",
        Friend: "F",
        Retrieved: "R"
    }
    const Graphics = {
        Player: null,
        Wolf: null,
        Pit: null,
        Friends: [],
        Ladder: null
    }
    window.addEventListener("load", () => {
        //https://www.w3schools.com/tags/canvas_drawimage.asp
        let pl = document.getElementById("player");
        Graphics.Player = pl;
        let w = document.getElementById("wolf");
        Graphics.Wolf = w;
        let f1 = document.getElementById("f1");
        let f2 = document.getElementById("f2");
        let f3 = document.getElementById("f3");
        Graphics.Friends.push(f1);
        Graphics.Friends.push(f2);
        Graphics.Friends.push(f3);
        game.Start();
    });

    function GameData() {
        return {
            score: 0,
            level: 1,
            rescued: 0,
            MapData: function(remoteGrid) {
                this.score = remoteGrid.player.score || 0;
                this.level = remoteGrid.level || 1;
                document.getElementById("level").innerText = "Level: " + this.level;
                document.getElementById("score").innerText = "Score: " + this.score;

            },
            NextLevel: function() {
                this.level++;
                document.getElementById("level").innerText = "Level: " + this.level;
                this.AddPoints(1000);
            },
            AddPoints: function(p) {
                this.score += p;
                document.getElementById("score").innerText = "Score: " + this.score;
            },
            AddFriend: function() {
                this.AddPoints(500);
            }
        }
    }

    function GridCell(x, y, type = "") {
        return {
            type: type,
            x: x,
            y: y,
            adj: [],
            visited: false,
            AddHint: function(hint) {
                if (hint && hint !== CellTypes.Retrieved) {
                    let arr = this.adj || [];
                    console.log("adding", hint);
                    arr.push(hint);
                    this.adj = [...new Set(arr)]
                    console.log("this", this);
                }
            },
            GetGFX: function(t) {
                if (t === CellTypes.Wolf) {
                    return "gray";
                } else if (t === CellTypes.Pit) {
                    return "brown";
                } else if (t === CellTypes.Ladder) {
                    return "green";
                } else if (t === CellTypes.Friend) {
                    return "blue"
                }
                return "yellow";
            }
        }
    }
    async function postData(data = {}, url = "/Project/api/game-backend.php") {

        console.log(Object.keys(data).map(function(key) {
            return "" + key + "=" + data[key]; // line break for wrapping only
        }).join("&"));
        let example = 1;
        if (example === 1) {
            // Default options are marked with *
            const response = await fetch(url, {
                method: 'POST', // *GET, POST, PUT, DELETE, etc.
                mode: 'cors', // no-cors, *cors, same-origin
                cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
                credentials: 'same-origin', // include, *same-origin, omit
                headers: {
                    //'Content-Type': 'application/json'
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                redirect: 'follow', // manual, *follow, error
                referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
                body: Object.keys(data).map(function(key) {
                    return "" + key + "=" + data[key]; // line break for wrapping only
                }).join("&") //JSON.stringify(data) // body data type must match "Content-Type" header
            });
            return response.json(); // parses JSON response into native JavaScript objects
        } else if (example === 2) {
            //making XMLHttpRequest awaitable
            //https://stackoverflow.com/a/48969580
            return new Promise(function(resolve, reject) {
                let xhr = new XMLHttpRequest();
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.open("POST", url);
                xhr.onload = function() {
                    if (this.status >= 200 && this.status < 300) {
                        resolve(xhr.response);
                    } else {
                        reject({
                            status: this.status,
                            statusText: xhr.statusText
                        });
                    }
                };
                xhr.onerror = function() {
                    reject({
                        status: this.status,
                        statusText: xhr.statusText
                    });
                };
                xhr.send(data);
            });
        } else if (example === 3) {
            //make jQuery awaitable
            //https://petetasker.com/using-async-await-jquerys-ajax
            //check if jQuery is present
            if (window.$) {
                let result;

                try {
                    result = await $.ajax({
                        url: url,
                        type: 'POST',
                        data: data
                    });

                    return result;
                } catch (error) {
                    console.error(error);
                }
            }
        }
    }

    function Grid() {
        return {
            wolfIndex: {
                x: Math.floor(Math.random() * (gridColumns - 1)),
                y: Math.floor(Math.random() * (gridRows - 1))
            },
            cells: [],
            hints: [],
            AddHint: function(x, y, hint) {
                this.cells[y][x].AddHint(hint);
            },
            ShowHints: function() {
                if (this.hints.length > 0) {
                    this.hints = [...new Set(this.hints)]; //make unique
                }
                let displayHints = this.hints.map((h) => {
                    return `<p>${h}</p>`
                });
                displayHints = displayHints.join("");
                console.log("display hints", displayHints);
                document.getElementById("hints").innerHTML = displayHints;
            },
            UpdateCell: function(cell) {
                console.log("updating cell", this.cells[cell.y][cell.x], "to", cell);
                let pType = this.cells[cell.y][cell.x].type;
                //update the existing cell with incoming data
                this.cells[cell.y][cell.x] = Object.assign(this.cells[cell.y][cell.x], cell);
                let cc = this.cells[cell.y][cell.x];
                //update the UI since server is sending just the cell instead of the full game state
                //so that means it's not returning the score for us
                if (cell.score && (pType === CellTypes.Friend || pType.length === 0) && cc.type === CellTypes.Retrieved) {
                    gameData.AddPoints(cc.score);
                }
                this.hints = [];
                for (let a of cc.adj) {
                    this.hints.push(grid.GetHint({
                        type: a
                    }));
                }
                this.ShowHints();
            },
            MapGrid: function(data) {
                //self.cells = data.cells;
                console.log("mapping", data);
                let self = this;
                gridRows = data.rows;
                gridColumns = data.cols;
                self.cells = data.cells;
                for (let y = 0; y < data.cols; y++) {
                    for (let x = 0; x < data.rows; x++) {
                        //need to create a dummy obj
                        //so we don't lose the built in functions
                        //console.log(x, y, GridCell(x, y, ""), data.cells[y][x]);
                        self.cells[y][x] = Object.assign(
                            GridCell(x, y, ""),
                            data.cells[y][x]
                        )
                    }
                }
                this.UpdateCell(this.cells[player.y][player.x]);
            },
            BuildGrid: function() {
                this.cells = [];
                if (!game.standalone) {
                    let self = this;
                    postData({
                            type: "load"
                        })
                        .then(data => {
                            console.log("server data: ", data)
                            self.MapGrid(data);
                            gameData.MapData(data);
                            player.x = data.player.x;
                            player.y = data.player.y;

                            game.Update();
                        });
                } else { //old standalone
                    let cellList = [];
                    const cellCount = gridRows * gridColumns;
                    const pits = Math.ceil(cellCount * .1);
                    for (let i = 0; i < pits; i++) {
                        cellList.push(CellTypes.Pit);
                    }
                    const friends = Math.ceil(cellCount * .05);
                    for (let i = 0; i < friends; i++) {
                        cellList.push(CellTypes.Friend);
                    }
                    const wolf = 1; //in case I decide to have multiple
                    for (let i = 0; i < wolf; i++) {
                        cellList.push(CellTypes.Wolf);
                    }
                    const ladder = 1; //in case I decide to have multiple
                    for (let i = 0; i < ladder; i++) {
                        cellList.push(CellTypes.Ladder);
                    }
                    //https://www.w3schools.com/jsref/jsref_fill.asp
                    if (cellList.length < cellCount) {
                        let diff = cellCount - cellList.length;
                        for (let i = 0; i < diff; i++) {
                            cellList.push("");
                        }
                        cellList.fill("", cellList.length, cellCount - 1);
                    }
                    const shuffleArray = array => {
                        for (let i = array.length - 1; i > 0; i--) {
                            const j = Math.floor(Math.random() * (i + 1));
                            const temp = array[i];
                            array[i] = array[j];
                            array[j] = temp;
                        }
                    }
                    shuffleArray(cellList);
                    //https://www.w3schools.com/jsref/jsref_unshift.asp
                    cellList.unshift(""); //make the first cell empty for the player

                    console.log("Cell List", cellList);
                    let levelHasLadder = false;
                    for (var y = 0; y < gridRows; y++) {
                        this.cells[y] = [];
                        for (var x = 0; x < gridColumns; x++) {
                            let cell = cellList.shift();
                            console.log("Assigning", cell);
                            this.cells[y][x] = GridCell(x, y, cell);
                        }
                    }
                    console.log("grid", this.cells);
                }
            },
            Draw: function(rows, cols) {
                rw = Math.floor(w / gridColumns);
                rh = Math.floor(h / gridRows);
                console.log("rw/rh", rw, rh);
                let sw = 0;
                let sh = 0;
                ctx.beginPath();
                ctx.rect(0, 0, w, h);
                ctx.strokeStyle = "black";
                ctx.lineWidth = 2;
                ctx.stroke();

                //draw vertical lines
                for (let i = 0; i < cols; i++) {
                    ctx.moveTo(sw, 0);
                    ctx.lineTo(sw, h);
                    sw += rw;
                }
                //draw horizontal lines
                for (let i = 0; i < rows; i++) {
                    ctx.moveTo(0, sh);
                    ctx.lineTo(w, sh);
                    sh += rh;
                }
                ctx.strokeStyle = "black";
                ctx.lineWidth = 2;
                ctx.stroke();

                for (var y = 0; y < gridRows; y++) {
                    for (var x = 0; x < gridColumns; x++) {
                        let cell = this.cells[y][x];
                        //draw hints
                        for (let i = 0; i < cell.adj.length; i++) {
                            const hint = cell.adj[i];
                            //for now ignore drawing rescued/retrieved friend hint
                            if (hint !== CellTypes.Retrieved) {
                                let cx = (x * rw) + (rw * ((.1 * i) + .1));
                                let cy = (y * rh) + (rh * .15);
                                console.log(rw, rh);
                                const hintSize = Math.ceil(rw * .1);
                                ctx.beginPath();
                                //ctx.rect(cx - (hintSize * .5), cy - (hintSize * .5), hintSize, hintSize);
                                ctx.fillStyle = cell.GetGFX(hint);
                                ctx.font = `${hintSize}px arial`
                                console.log(ctx.font, hintSize);
                                ctx.fillText(hint, cx - (hintSize * .5), cy - (hintSize * .5)); //, hintSize, hintSize);
                                ctx.fill();
                                ctx.closePath();
                            }

                        }

                        //draw friends
                        if (cell.visited) {
                            if (cell.type === CellTypes.Retrieved) {
                                if (isNaN(cell.friend)) {
                                    cell.friend = Math.floor(Math.random() * (Graphics.Friends.length - 1));
                                }
                                if (cell.friend >= 0) {
                                    let px = (cell.x * rw) + (rw / 2);
                                    let py = (cell.y * rh) + (rh / 2);
                                    let sizeX = rw * .5 * .5;
                                    let sizeY = rh * .5 * .5;
                                    //x - sizeX /*this.s / 2*/ , y - sizeY /*sthis.s / 2*/ , sizeX * 2, sizeY * 2
                                    ctx.beginPath();
                                    console.log("drawing friend", cell.friend, Graphics.Friends[cell.friend]);
                                    ctx.drawImage(Graphics.Friends[cell.friend], px - sizeX, py - sizeY, sizeX * 2, sizeY * 2);

                                }
                            }
                        }
                    }
                }
                ctx.beginPath();
            },
            GetHint: function(cell) {
                console.log("getHint", cell);
                if (game.standalone) {
                    try {
                        grid.AddHint(player.x, player.y, cell.type);
                        //grid[py][px].AddHint((cell.type || ""));
                    } catch (e) {
                        console.log("Hint exception", e);
                    }
                }
                console.log("Checking cell type: ", cell.type);
                if (cell.type === CellTypes.Pit) {
                    return "You hear an echo in the distance";
                } else if (cell.type === CellTypes.Wolf) {
                    return "You hear howling in the distance";
                } else if (cell.type === CellTypes.Ladder) {
                    return "You feel a warm breeze";
                } else if (cell.type === CellTypes.Friend) {
                    return "You hear someone calling your attention";
                }
                return "";
            },
            CheckCell: function(x, y) {
                //TODO get this from backend
                if (!game.standalone) {
                    let self = this;
                    postData({
                            type: "check",
                            x: x,
                            y: y
                        })
                        .then(cell => {
                            self.cells[y][y] = cell;
                        });
                } else {
                    const cc = this.cells[y][x];
                    cc.visited = true;
                    console.log("Pos", `${x},${y}`, "Current cell", JSON.stringify(this.cells[y][x]), "grid", this);
                    //check current

                    if (cc.type === CellTypes.Wolf) {
                        console.log("Lose: Wolf")
                        game.GameOver("Eaten by wolf");
                        return;
                    } else if (cc.type === CellTypes.Pit) {
                        console.log("Lose: Pit");
                        game.GameOver("Fell in a pit");
                        return;
                    } else if (cc.type === CellTypes.Ladder) {
                        game.NextLevel();
                        return;
                    } else if (cc.type === CellTypes.Friend) {
                        cc.type = CellTypes.Retrieved; //retrieved
                        gameData.AddFriend();
                    }
                    //check adjacents
                    let adj = ["", "", "", ""]; //up, down, left, right
                    this.hints = [];
                    try {
                        adj[0] = this.cells[y - 1][x] || {};
                        let hint = this.GetHint(adj[0]);
                        if (hint) {
                            this.hints.push(hint);
                        }
                    } catch (e) {
                        //console.log("Coord error", e);
                    }
                    try {
                        adj[1] = this.cells[y + 1][x] || {};
                        let hint = this.GetHint(adj[1]);
                        if (hint) {
                            this.hints.push(hint);
                        }
                    } catch (e) {
                        //console.log("Coord error", e);
                    }
                    try {
                        adj[2] = this.cells[y][x - 1] || {};
                        let hint = this.GetHint(adj[2]);
                        if (hint) {
                            this.hints.push(hint);
                        }
                    } catch (e) {
                        //console.log("Coord error", e);
                    }
                    try {
                        adj[3] = this.cells[y][x + 1] || {};
                        let hint = this.GetHint(adj[3]);
                        if (hint) {
                            this.hints.push(hint);
                        }
                    } catch (e) {
                        //console.log("Coord error", e);
                    }
                }
            }
        }
    }

    function Player() {
        return {
            x: 0,
            y: 0,
            s: 50, //not used anymore
            c: "red", //not used anymore
            Reset: function() {
                this.x = 0;
                this.y = 0;
            },
            Draw: function() {
                ctx.beginPath();
                let px = (this.x * rw) + (rw / 2);
                let py = (this.y * rh) + (rh / 2);
                let sizeX = rw * .5 * .5;
                let sizeY = rh * .5 * .5;
                ctx.drawImage(Graphics.Player, px - sizeX /*this.s / 2*/ , py - sizeY /*sthis.s / 2*/ , sizeX * 2, sizeY * 2);
                //ctx.rect(px - sizeX /*this.s / 2*/ , py - sizeY /*sthis.s / 2*/ , sizeX * 2, sizeY * 2);
                //ctx.strokeStyle = this.c;
                //ctx.fillStyle = this.c;
                //ctx.fill();
                //ctx.stroke();
            },
            Move: function(x, y) {
                let self = this;
                if (!game.standalone) {
                    postData({
                            type: "move",
                            x: x,
                            y: y
                        })
                        .then(data => {
                            console.log("data", data);
                            if (data) {
                                if (data.event) {
                                    if (data.event === "died") {
                                        game.GameOver(data.reason);
                                    } else if (data.event === "next_level") {
                                        game.NextLevel();
                                    }
                                    return;
                                } else {
                                    self.x = data.x;
                                    self.y = data.y;
                                    //grid.MapGrid(data);
                                    grid.UpdateCell(data);
                                    //let cur_cell = grid.cells[self.y][self.x];

                                }
                                //console.log("adjacents", adj, hints);
                            }
                            game.Update();
                        })
                } else {
                    this.x += x;
                    this.y += y;
                    if (this.x < 0) {
                        this.x++;
                    }
                    if (this.x >= (gridColumns)) {
                        this.x--;
                    }
                    if (this.y < 0) {
                        this.y++;
                    }
                    if (this.y >= gridRows) {
                        this.y--;
                    }
                    console.log(this.x, this.y, gridRows, gridColumns);
                }
            }
        }
    }

    function Game() {
        return {
            gameOver: false,
            Start: function() {
                grid.BuildGrid();

                //move(0, 0);
                //this.Update();
            },
            Erase: function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            },
            Update: function() {
                console.log("Drawing");
                this.Erase();
                //grid.CheckCell(player.x, player.y);
                grid.Draw(gridRows, gridColumns);
                player.Draw();
            },

            GameOver: function(reason = "") {
                this.Erase();
                this.gameOver = true;
                const mx = canvas.width * .5;
                const my = canvas.height * .5;
                const yspacing = canvas.height * .05;
                ctx.fillStyle = "red";
                ctx.font = "30px Arial";
                ctx.textAlign = 'center';
                ctx.fillText("Game Over", mx, my - yspacing);
                ctx.fillText(reason, mx, my + yspacing);
                ctx.fillText("Click the screen to play again", mx, my + yspacing * 2);
                if (game.standalone) {
                    //save standalone mode score (not used in my project)
                    postData({
                        score: gameData.score,
                        level: gameData.level,
                        rescued: gameData.rescued
                    }, "/Project/api/save_score.php").then(data => {
                        console.log(data);
                        //quick, brief example (you wouldn't want to use alert)
                        if (data.status === 200) {
                            //saved successfully
                            alert(data.message);
                        } else {
                            //some error occurred, maybe want to handle it before resetting
                            alert(data.message);
                        }
                    })
                }
                canvas.addEventListener("click", () => {
                    window.location.reload();
                })
            },
            NextLevel: function() {
                this.Erase();
                console.log("Next level");
                gameData.NextLevel();
                ctx.fillStyle = "green";
                const mx = canvas.width * .5;
                const my = canvas.height * .5;
                const yspacing = canvas.height * .05;
                ctx.font = "30px Arial";
                ctx.textAlign = 'center';
                ctx.fillStyle = "green"
                ctx.fillText("Next Level!", mx, my - yspacing);
                //ctx.fillText(reason, mx, my + yspacing);
                ctx.fillText("Click the screen to continue", mx, my + yspacing * 2);
                canvas.addEventListener("click", () => {
                    grid.BuildGrid();
                    if (game.standalone) {
                        player.Reset();
                        this.Update();
                    }
                })

            }
        }
    }
    var player = Player();
    var grid = Grid();
    var game = Game();
    var gameData = GameData();
    //end game data

    //called via buttons
    function move(x, y) {
        if (game.gameOver) {
            return;
        }
        player.Move(x, y);
        if (game.standalone) {
            game.Update();
        }
    }



    //
</script>
<style>
    canvas {
        width: 100%;
        height: 100%;
    }

    html {
        overflow: hidden;
    }
</style>
<?php require(__DIR__ . "/../../partials/footer.php");
