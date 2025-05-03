const { useState, useEffect } = React;

function SnapshotViewer({ bindToWindow, windowKey }) {
  const [grid, setGrid] = useState([]);

  useEffect(() => {
    if (bindToWindow && windowKey) {
      window[windowKey] = setGrid;
    }
  }, []);

  return React.createElement(
    "div",
    { className: "d-inline-block" },
    grid.map((row, i) =>
      React.createElement(
        "div",
        { key: i, style: { display: "flex" } },
        row.map((cell, j) =>
          React.createElement("div", {
            key: j,
            style: {
              width: "12px",
              height: "12px",
              backgroundColor: cell ? "green" : "#eee",
              border: "1px solid #ccc",
            },
          })
        )
      )
    )
  );
}

ReactDOM.render(
  React.createElement(SnapshotViewer, { bindToWindow: true, windowKey: "setBeforeSnapshot" }),
  document.getElementById("react-snapshot-before")
);

ReactDOM.render(
  React.createElement(SnapshotViewer, { bindToWindow: true, windowKey: "setAfterSnapshot" }),
  document.getElementById("react-snapshot-after")
);
