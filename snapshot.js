function createSnapshot(containerId, windowKey) {
  const container = document.getElementById(containerId);
  if (!container) return;

  window[windowKey] = function(grid) {
    container.innerHTML = ""; // clear previous snapshot

    grid.forEach((row) => {
      const rowDiv = document.createElement("div");
      rowDiv.style.display = "flex";

      row.forEach((cell) => {
        const cellDiv = document.createElement("div");
        cellDiv.style.width = "12px";
        cellDiv.style.height = "12px";
        cellDiv.style.backgroundColor = cell ? "green" : "#eee";
        cellDiv.style.border = "1px solid #ccc";
        rowDiv.appendChild(cellDiv);
      });

      container.appendChild(rowDiv);
    });
  };
}

// Attach snapshot handlers to window
window.addEventListener("DOMContentLoaded", () => {
  createSnapshot("react-snapshot-before", "setBeforeSnapshot");
  createSnapshot("react-snapshot-after", "setAfterSnapshot");
});
