const express = require('express');
const app = express();
const http = require('http');
const redis = require('redis');
const server = http.createServer(app);
const { Server } = require("socket.io");

const io = new Server(server, {
    cors: {
      origin: "*"
    }
});

io.on('connection', async (socket) => {
    console.log("socket client connected");

    const redisClient = redis.createClient({
        socket: {
            host: 'redis'
        }
    });
    
    redisClient.connect().then((value) => {
        console.log('redis connected')
    })
    
    redisClient.subscribe('message', function(message, channel) {
        socket.emit(channel, message)
    }).then(() => {
        console.log('subscribed to "message" channel')
    });
});
  
server.listen(9000, () => {
    console.log('listening on *:9000');
});