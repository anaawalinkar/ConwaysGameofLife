const rows = 25;
const cols = 25;
let grid = [];
window.interval = null;
window.gameRunning = false;
window.generationCount = 0;

function createGrid() {
  const gridContainer = document.getElementById('grid');
  grid = [];
  gridContainer.innerHTML = '';
  gridContainer.style.gridTemplateColumns = `repeat(${cols}, 20px)`;

  for (let i = 0; i < rows; i++) {
    grid[i] = [];
    for (let j = 0; j < cols; j++) {
      const cell = document.createElement('div');
      cell.classList.add('cell');
      cell.addEventListener('click', () => {
        cell.classList.toggle('alive');
        grid[i][j] = !grid[i][j];
        if (!window.gameRunning && window.setBeforeSnapshot) {
          window.setBeforeSnapshot(grid.map(row => [...row]));
        }
        if (window.setAfterSnapshot) {
          window.setAfterSnapshot([]);
        }
      });
      grid[i][j] = false;
      gridContainer.appendChild(cell);
    }
  }
}

function getCell(row, col) {
  return document.getElementById('grid').children[row * cols + col];
}

function updateStatus(running) {
  const status = document.getElementById("status");
  status.innerText = running ? "🟢 Game is running" : "🔴 Game is stopped";
  status.classList.toggle("text-success", running);
  status.classList.toggle("text-danger", !running);
}

function updateGenerationDisplay() {
  document.getElementById("generation").innerText = `Generation: ${window.generationCount}`;
}

function nextGeneration() {
  const newGrid = grid.map(arr => [...arr]);
  for (let i = 0; i < rows; i++) {
    for (let j = 0; j < cols; j++) {
      let alive = 0;
      for (let x = -1; x <= 1; x++) {
        for (let y = -1; y <= 1; y++) {
          if (x === 0 && y === 0) continue;
          const r = i + x, c = j + y;
          if (r >= 0 && r < rows && c >= 0 && c < cols && grid[r][c]) {
            alive++;
          }
        }
      }

      if (grid[i][j]) {
        newGrid[i][j] = alive === 2 || alive === 3;
      } else {
        newGrid[i][j] = alive === 3;
      }
    }
  }

  // ✅ Increment generation
  window.generationCount++;
  if (window.incrementGeneration) window.incrementGeneration();

  grid = newGrid;
  updateGrid();
  updateGenerationDisplay();
}

function updateGrid() {
  for (let i = 0; i < rows; i++) {
    for (let j = 0; j < cols; j++) {
      const cell = getCell(i, j);
      cell.classList.toggle('alive', grid[i][j]);
    }
  }
}

function startGame() {
  if (!window.interval) {
    window.gameRunning = true;
    updateStatus(true);
    if (window.setBeforeSnapshot) {
      window.setBeforeSnapshot(grid.map(row => [...row]));
    }

    const speed = parseInt(document.getElementById("speedSelect").value);
    window.interval = setInterval(nextGeneration, speed);

    if (window.setStartSession) {
      window.setStartSession();
    }
  }
}

function stopGame() {
  clearInterval(window.interval);
  window.interval = null;
  window.gameRunning = false;
  updateStatus(false);

  if (window.setAfterSnapshot) {
    window.setAfterSnapshot(grid.map(row => [...row]));
  }

  if (window.setStopSession) {
    window.setStopSession();
  }
}

function nextGen() {
  nextGeneration();
}

function next23() {
  for (let i = 0; i < 23; i++) nextGeneration();
}

function resetGame() {
  clearInterval(window.interval);
  window.interval = null;
  window.gameRunning = false;
  window.generationCount = 0;
  createGrid();
  updateStatus(false);
  updateGenerationDisplay();

  if (window.setAfterSnapshot) {
    window.setAfterSnapshot([]);
  }

  if (window.setStopSession) {
    window.setStopSession();
  }
}

function loadPattern(pattern) {
  resetGame();
  const midRow = Math.floor(rows / 2);
  const midCol = Math.floor(cols / 2);

  const setAlive = (r, c) => {
    if (r >= 0 && r < rows && c >= 0 && c < cols) {
      grid[r][c] = true;
      getCell(r, c).classList.add('alive');
    }
  };

  switch (pattern) {
    case "block":
      setAlive(midRow, midCol);
      setAlive(midRow, midCol + 1);
      setAlive(midRow + 1, midCol);
      setAlive(midRow + 1, midCol + 1);
      break;
    case "blinker":
      setAlive(midRow, midCol - 1);
      setAlive(midRow, midCol);
      setAlive(midRow, midCol + 1);
      break;
    case "beacon":
      setAlive(midRow, midCol);
      setAlive(midRow, midCol + 1);
      setAlive(midRow + 1, midCol);
      setAlive(midRow + 1, midCol + 1);
      setAlive(midRow + 2, midCol + 2);
      setAlive(midRow + 2, midCol + 3);
      setAlive(midRow + 3, midCol + 2);
      setAlive(midRow + 3, midCol + 3);
      break;
    case "glider":
      setAlive(midRow, midCol + 1);
      setAlive(midRow + 1, midCol + 2);
      setAlive(midRow + 2, midCol);
      setAlive(midRow + 2, midCol + 1);
      setAlive(midRow + 2, midCol + 2);
      break;
  }

  if (!window.gameRunning && window.setBeforeSnapshot) {
    window.setBeforeSnapshot(grid.map(row => [...row]));
  }
  if (window.setAfterSnapshot) {
    window.setAfterSnapshot([]);
  }
}

// ✅ Ensure grid initializes after page is ready
window.addEventListener('DOMContentLoaded', () => {
  createGrid();

  document.getElementById("speedSelect").addEventListener("change", () => {
    if (window.gameRunning) {
      clearInterval(window.interval);
      const newSpeed = parseInt(document.getElementById("speedSelect").value);
      window.interval = setInterval(nextGeneration, newSpeed);
    }
  });
});

// ✅ Expose generation function globally (for React snapshots and session tracking)
window.nextGeneration = nextGeneration;
