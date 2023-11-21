const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    credentials: true,
  },
});
httpServer.listen(5173, () => {
  console.log("listening on *:5173");
});

io.on("connection", (socket) => {
  console.log(`client ${socket.id} has connected`);
  socket.on("sent", (message) => {
    console.log(`Received: ${message}`)
    socket.emit("echo", message);
    console.log(`Sent: ${message}`)
  });
});
