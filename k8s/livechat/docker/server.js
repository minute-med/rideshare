const express = require('express');
const app = express();
const http = require('http');
const redis = require('redis');
const server = http.createServer(app);
const { Server } = require("socket.io");

const port = process.env.APP_PORT || 9000

const io = new Server(server, {
    cors: {
      origin: "*"
    }
});

io.on('connection', async (socket) => {
    console.log("socket client connected");

    const redisClient = redis.createClient({
        socket: {
            host: process.env.REDIS_HOST
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

server.listen(port, () => {
    console.log(`listening on *:${port}`);
});