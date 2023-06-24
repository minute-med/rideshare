import { reactive } from "vue";
import { io } from "socket.io-client";

export const state = reactive({
  connected: false,
  messageEvents: [],
});

// "undefined" means the URL will be computed from the `window.location` object
const URL = process.env.NODE_ENV === "production" ? 'http://34.126.114.68:9000' : "http://localhost:9000";

console.log('process.env.(VITE_)LIVECHAT_URL')
console.log(process.env.LIVECHAT_URL)

export const socket = io(URL);

socket.on("connect", () => {
  state.connected = true;
  console.log('client socket connected')
});

socket.on("disconnect", () => {
  state.connected = false;
});
