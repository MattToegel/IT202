<div style="height: 70%; width: 70%;">
    <div id="stats">
        <span id="level">Level: 1</span><span id="score">Score: 0</span>
    </div>
    <canvas id="board" width="1024px" height="1024px">
    </canvas>

    <div>
        <table>
            <tr>
                <td></td>
                <td style="text-align:center"><button onClick="move(0,-1)">Up</button></td>
                <td></td>
            </tr>
            <tr>
                <td><button onClick="move(-1,0)">Left</button></td>
                <td></td>
                <td><button onClick="move(1,0)">Right</button></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align:center"><button onClick="move(0,1)">Down</button></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <span id="hints"></span>
    </div>
</div>
<script>
    const canvas = document.getElementById("board");
    const ctx = canvas.getContext("2d");
    let w = canvas.width;
    let h = canvas.height;
    console.log("w/h", w, h);
    let gridRows = 4;
    let gridColumns = 6;
    let rw = 0; //Math.ceil(w/gridColumns);
    let rh = 0; //Math.ceil(h/gridRows);
    const CellTypes = {
        Wolf: "W",
        Ladder: "L",
        Pit: "P",
        Friend: "F"
    }

    function GameData() {
        return {
            score: 0,
            level: 1,
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

    function GridCell(type = "") {
        return {
            type: type,
            adj: [],
            AddHint: function(hint) {
                if (hint) {
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
                    return "blue;"
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
            AddHint: function(x, y, hint) {
                this.cells[y][x].AddHint(hint);
            },
            BuildGrid: function() {
                this.cells = [];
                //rules
                //wolf can't be on bottom left corner
                //spawn X pits based on level (up to a maximum)
                //TODO limit pit rules as not to make impossible level
                //Spawn 1 ladder per level to progress to the next stage
                //
                let levelHasLadder = false;
                for (var y = 0; y < gridRows; y++) {
                    this.cells[y] = [];
                    for (var x = 0; x < gridColumns; x++) {
                        if (x === 0 && y === 0) {
                            //skip start cell
                            this.cells[y][x] = GridCell("");
                            continue;
                        }
                        let cell = Math.random() > .8 ? CellTypes.Pit : "";
                        this.cells[y][x] = GridCell(cell);
                        if (x === this.wolfIndex.x && y === this.wolfIndex.y) {
                            this.cells[y][x] = GridCell(CellTypes.Wolf);
                        }
                        if (!levelHasLadder && this.cells[y][x].type.length === 0) {
                            cell = Math.random() > .5 ? CellTypes.Ladder : "";
                            this.cells[y][x] = GridCell(cell);
                            levelHasLadder = true;
                        }
                    }
                }
                console.log("grid", this.cells);
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


                for (let i = 0; i < cols; i++) {
                    ctx.moveTo(sw, 0);
                    ctx.lineTo(sw, h);
                    sw += rw;
                    //console.log("sw", sw);
                }

                for (let i = 0; i < rows; i++) {
                    ctx.moveTo(0, sh);
                    ctx.lineTo(w, sh);
                    sh += rh;
                    //console.log("sh", sh);
                }
                ctx.strokeStyle = "black";
                ctx.lineWidth = 2;
                ctx.stroke();

                for (var y = 0; y < gridRows; y++) {
                    for (var x = 0; x < gridColumns; x++) {
                        let cell = this.cells[y][x];
                        for (let i = 0; i < cell.adj.length; i++) {
                            const hint = cell.adj[i];
                            let cx = (x * rw) + (rw * ((.1 * i) + .1));
                            let cy = (y * rh) + (rh * .1);
                            const hintSize = 10;
                            ctx.beginPath();
                            ctx.rect(cx - (hintSize * .5), cy - (hintSize * .5), hintSize, hintSize);
                            ctx.fillStyle = cell.GetGFX(hint);
                            ctx.fill();

                        }
                    }
                }
                ctx.beginPath()
            },
            GetHint: function(cell) {
                console.log("getHint", cell);
                try {
                    grid.AddHint(player.x, player.y, cell.type);
                    //grid[py][px].AddHint((cell.type || ""));
                } catch (e) {
                    console.log("Hint exception", e);
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

                const cc = this.cells[y][x];
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
                    cc.type = "R"; //retrieved
                    gameData.AddFriend();
                }
                //check adjacents
                let adj = ["", "", "", ""]; //up, down, left, right
                let hints = [];
                try {
                    adj[0] = this.cells[y - 1][x] || {};
                    let hint = this.GetHint(adj[0]);
                    if (hint) {
                        hints.push(hint);
                    }
                } catch (e) {
                    //console.log("Coord error", e);
                }
                try {
                    adj[1] = this.cells[y + 1][x] || {};
                    let hint = this.GetHint(adj[1]);
                    if (hint) {
                        hints.push(hint);
                    }
                } catch (e) {
                    //console.log("Coord error", e);
                }
                try {
                    adj[2] = this.cells[y][x - 1] || {};
                    let hint = this.GetHint(adj[2]);
                    if (hint) {
                        hints.push(hint);
                    }
                } catch (e) {
                    //console.log("Coord error", e);
                }
                try {
                    adj[3] = this.cells[y][x + 1] || {};
                    let hint = this.GetHint(adj[3]);
                    if (hint) {
                        hints.push(hint);
                    }
                } catch (e) {
                    //console.log("Coord error", e);
                }
                if (hints.length > 0) {
                    hints = [...new Set(hints)]; //make unique
                }
                let displayHints = hints.map((h) => {
                    return `<p>${h}</p>`
                });
                displayHints = displayHints.join("");
                console.log("display hints", displayHints);
                document.getElementById("hints").innerHTML = displayHints;
                console.log("adjacents", adj, hints);
            }
        }
    }

    function Player() {
        return {
            x: 0,
            y: 0,
            s: 50,
            c: "red",
            Reset: function() {
                this.x = 0;
                this.y = 0;
            },
            Draw: function() {
                ctx.beginPath();
                let px = (this.x * rw) + (rw / 2);
                let py = (this.y * rh) + (rh / 2);

                ctx.rect(px - this.s / 2, py - this.s / 2, this.s, this.s);
                ctx.strokeStyle = this.c;
                ctx.fillStyle = this.c;
                ctx.fill();
                ctx.stroke();
            },
            Move: function(x, y) {
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

    function Game() {
        return {
            gameOver: false,
            Start: function() {
                grid.BuildGrid();
                move(0, 0);
                this.Update();
            },
            Erase: function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            },
            Update: function() {
                this.Erase();
                grid.CheckCell(player.x, player.y);
                grid.Draw(gridRows, gridColumns);
                player.Draw();
            },

            GameOver: function(reason = "") {
                this.Erase();
                this.gameOver = true;
                const mx = canvas.width * .5;
                const my = canvas.height * .5;
                const yspacing = canvas.height * .05;
                ctx.font = "30px Arial";
                ctx.textAlign = 'center';
                ctx.fillText("Game Over", mx, my - yspacing);
                ctx.fillText(reason, mx, my + yspacing);
                ctx.fillText("Click the screen to play again", mx, my + yspacing * 2);
                canvas.addEventListener("click", () => {
                    window.location.reload();
                })
            },
            NextLevel: function() {
                this.Erase();
                console.log("Next level");
                gameData.NextLevel();
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
                    player.Reset();
                    this.Update();
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
        game.Update();
    }



    game.Start();
</script>
<style>
    canvas {
        width: 100%;
        height: 100%;
    }
</style>