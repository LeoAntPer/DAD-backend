const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"],
        credentials: true,
    },
});
httpServer.listen(5174, () => {
    console.log("listening on *:5174");
});

io.on("connection", (socket) => {
    console.log(`client ${socket.id} has connected`);

    /*
    socket.on("joinRoom", (room) => {
        socket.join(room);
    });

    socket.on("leaveRoom", (room) => {
        socket.leave(room);
    });
*/
    socket.on('newTransaction', function (transaction) {
        //socket.join(transaction.payment_reference)
        socket.in('vcard').emit('newTransaction', transaction)
        //socket.leave(transaction.payment_reference)
    })

    socket.on('insertedUser', function (user) {
        socket.in('administrator').emit('insertedUser', user)
    })

    socket.on('updatedUser', function (user) {
        console.log(user)
        socket.in('administrator').emit('updatedUser', user)
    })

    socket.on('loggedIn', function (user) {
        socket.join(user.id)
        if (user.user_type == 'A') {
            socket.join('administrator')
        } else {
            socket.join('vcard')
        }
    })
    socket.on('loggedOut', function (user) {
        socket.leave(user.id)
        socket.leave('administrator')
    })
});
