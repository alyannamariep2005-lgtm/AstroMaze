// =================================
// ASTROMAZE - GAME SETTINGS
// =================================

// Size of each maze tile
const TILE_SIZE = 32;

// Maze dimensions
const MAZE_COLUMNS = 21;
const MAZE_ROWS = 15;

// Game information
let score = 0;
let collectedOrbs = 0;
let fuel = 40;
let gameState = "playing";

let recordSaved = false;

async function saveGameRecord(status) {
  if (recordSaved) return;

  recordSaved = true;

  try {
    const response = await fetch("save_game.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        score: score,
        orbs_collected: collectedOrbs,
        fuel_remaining: fuel,
        game_status: status,
      }),
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "Unable to save game record.");
    }

    console.log(result.message);
  } catch (error) {
    recordSaved = false;
    console.error("Game record saving failed:", error);
  }
}

// Player starting position
let playerRow = 1;
let playerCol = 1;

// Player's starting direction
let playerDirection = "right";

let gameOver = false;
console.log("AstroMaze is ready!");
console.log("Maze size:", MAZE_COLUMNS, "columns x", MAZE_ROWS, "rows");

// =================================
// MAZE GRID
// =================================

// Create an empty maze array
let maze = [];

// Store the positions of energy orbs
let orbs = [];

// Store the positions of fuel pickups
let fuels = [];

// Fill the maze with walls
function createMazeGrid() {
  for (let row = 0; row < MAZE_ROWS; row++) {
    let currentRow = [];

    for (let col = 0; col < MAZE_COLUMNS; col++) {
      currentRow.push(1);
    }

    maze.push(currentRow);
  }

  console.log("Maze grid created!");
  console.table(maze);
}

// =================================
// RANDOM MAZE GENERATION
// =================================

function generateMaze(row, col) {
  // Mark the starting cell as a path
  maze[row][col] = 0;

  // Possible movement directions
  let directions = [
    [-2, 0], // Up
    [2, 0], // Down
    [0, -2], // Left
    [0, 2], // Right
  ];

  // Shuffle directions randomly
  directions.sort(() => Math.random() - 0.5);

  // Explore each direction
  for (let [rowChange, colChange] of directions) {
    let newRow = row + rowChange;
    let newCol = col + colChange;

    // Check if the next cell is inside the maze
    if (
      newRow > 0 &&
      newRow < MAZE_ROWS - 1 &&
      newCol > 0 &&
      newCol < MAZE_COLUMNS - 1 &&
      maze[newRow][newCol] === 1
    ) {
      // Remove the wall between the two cells
      maze[row + rowChange / 2][col + colChange / 2] = 0;

      // Continue exploring
      generateMaze(newRow, newCol);
    }
  }
}

// =================================
// PLACE ENERGY ORBS
// =================================

function placeOrbs() {
  orbs = [];

  const totalOrbs = 10;

  while (orbs.length < totalOrbs) {
    let randomRow = Math.floor(Math.random() * MAZE_ROWS);
    let randomCol = Math.floor(Math.random() * MAZE_COLUMNS);

    if (maze[randomRow][randomCol] === 0) {
      if (randomRow !== playerRow || randomCol !== playerCol) {
        let alreadyOccupied = orbs.some(
          (orb) => orb.row === randomRow && orb.col === randomCol,
        );

        if (!alreadyOccupied) {
          orbs.push({
            row: randomRow,
            col: randomCol,
          });
        }
      }
    }
  }

  console.log("Energy orbs placed:", orbs);
}

// =================================
// PLACE FUEL PICKUPS
// =================================

function placeFuels() {
  fuels = [];

  const totalFuels = 3;

  while (fuels.length < totalFuels) {
    let randomRow = Math.floor(Math.random() * MAZE_ROWS);
    let randomCol = Math.floor(Math.random() * MAZE_COLUMNS);

    if (maze[randomRow][randomCol] === 0) {
      if (randomRow !== playerRow || randomCol !== playerCol) {
        let alreadyOccupied =
          orbs.some((orb) => orb.row === randomRow && orb.col === randomCol) ||
          fuels.some(
            (fuel) => fuel.row === randomRow && fuel.col === randomCol,
          );

        if (!alreadyOccupied) {
          fuels.push({
            row: randomRow,
            col: randomCol,
          });
        }
      }
    }
  }

  console.log("Fuel pickups placed:", fuels);
}

// =================================
// COLLECT ENERGY ORBS
// =================================

function collectOrb() {
  if (gameState !== "playing") {
    return;
  }
  let orbIndex = orbs.findIndex(
    (orb) => orb.row === playerRow && orb.col === playerCol,
  );

  if (orbIndex !== -1) {
    orbs.splice(orbIndex, 1);

    collectedOrbs++;
    score += 10;

    document.getElementById("orbs").textContent = collectedOrbs;
    document.getElementById("score").textContent = score;

    console.log("Orb collected! Current score:", score);
  }
}
// =================================
// CHECK VICTORY
// =================================

function checkVictory() {
  if (gameState !== "playing") {
    return;
  }

  if (orbs.length === 0) {
    gameState = "won";
    gameOver = true;

    saveGameRecord("won");

    document.getElementById("victory-screen").style.display = "flex";

    console.log("MISSION COMPLETE!");
  }
}
// =================================
// COLLECT FUEL
// =================================

function collectFuel() {
  if (gameState !== "playing") {
    return;
  }
  let fuelIndex = fuels.findIndex(
    (item) => item.row === playerRow && item.col === playerCol,
  );

  if (fuelIndex !== -1) {
    fuels.splice(fuelIndex, 1);

    fuel += 20;

    if (fuel > 100) {
      fuel = 100;
    }

    document.getElementById("fuel").textContent = fuel + "%";

    console.log("Fuel collected! Current fuel:", fuel);
  }
}

// =================================
// FUEL CONSUMPTION
// =================================

// =================================
// FUEL CONSUMPTION
// =================================

function drainFuel() {
  // Only consume fuel while playing
  if (gameState !== "playing") {
    return;
  }

  if (fuel > 0) {
    fuel--;

    document.getElementById("fuel").textContent = fuel + "%";

    console.log("Fuel remaining:", fuel);
  }

  if (fuel <= 0) {
    fuel = 0;
    gameState = "lost";
    gameOver = true;

    saveGameRecord("lost");

    document.getElementById("fuel").textContent = "0%";

    document.getElementById("game-over-screen").style.display = "flex";

    console.log("GAME OVER!");
  }
}
// =================================
// DISPLAY THE MAZE
// =================================

function renderMaze() {
  console.log("Rendering player at:", playerRow, playerCol);
  const mazeElement = document.getElementById("maze");

  // Clear the previous maze
  mazeElement.innerHTML = "";

  // Loop through every row
  for (let row = 0; row < MAZE_ROWS; row++) {
    // Loop through every column
    for (let col = 0; col < MAZE_COLUMNS; col++) {
      const cell = document.createElement("div");

      if (maze[row][col] === 1) {
        cell.classList.add("wall");
      } else if (row === playerRow && col === playerCol) {
        cell.classList.add("player");

        // Rotate the rocket based on its direction
        if (playerDirection === "right") {
          cell.style.transform = "rotate(0deg)";
        } else if (playerDirection === "down") {
          cell.style.transform = "rotate(90deg)";
        } else if (playerDirection === "left") {
          cell.style.transform = "rotate(180deg)";
        } else if (playerDirection === "up") {
          cell.style.transform = "rotate(270deg)";
        }
      } else {
        // Check if an energy orb exists in this cell
        let orbExists = orbs.some((orb) => orb.row === row && orb.col === col);

        if (orbExists) {
          cell.classList.add("orb");
        } else {
          // Check if a fuel pickup exists in this cell
          let fuelExists = fuels.some(
            (fuel) => fuel.row === row && fuel.col === col,
          );

          if (fuelExists) {
            cell.classList.add("fuel");
          } else {
            cell.classList.add("path");
          }
        }
      }

      mazeElement.appendChild(cell);
    }
  }
}

// =================================
// PLAYER MOVEMENT
// =================================

document.addEventListener("keydown", function (event) {
  if (gameState !== "playing") {
    return;
  }
  console.log("Key pressed:", event.key);

  let newRow = playerRow;
  let newCol = playerCol;

  switch (event.key) {
    case "ArrowUp":
    case "w":
    case "W":
      newRow--;
      playerDirection = "up";
      break;

    case "ArrowDown":
    case "s":
    case "S":
      newRow++;
      playerDirection = "down";
      break;

    case "ArrowLeft":
    case "a":
    case "A":
      newCol--;
      playerDirection = "left";
      break;

    case "ArrowRight":
    case "d":
    case "D":
      newCol++;
      playerDirection = "right";
      break;

    default:
      return;
  }

  // Prevent the browser from scrolling
  event.preventDefault();

  // Check if the destination is walkable
  console.log("Trying to move to:", newRow, newCol);
  console.log("Destination value:", maze[newRow][newCol]);

  if (maze[newRow][newCol] === 0) {
    playerRow = newRow;
    playerCol = newCol;

    console.log("Movement successful!");

    collectOrb();
    checkVictory();
    collectFuel();

    renderMaze();
  }
});

// =================================
// START THE GAME
// =================================

createMazeGrid();
generateMaze(1, 1);
placeOrbs();
placeFuels();
renderMaze();
// Start fuel consumption every second
setInterval(drainFuel, 500);

// =================================
// RESTART GAME
// =================================

document
  .getElementById("restart-button")
  .addEventListener("click", function () {
    window.location.reload();
  });

// Victory screen restart button
document
  .getElementById("victory-restart-button")
  .addEventListener("click", function () {
    window.location.reload();
  });
