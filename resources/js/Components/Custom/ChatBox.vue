<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue'
import { socket } from "@/socket";
import { onMounted } from 'vue';

const props = defineProps({
    tripId: {
        type: Number,
        required: true,
    },
    sender: {
        type: Object,
        required: true,
    },
    userList: {
        type: Array,
        required: true
    },
    messages: {
        type: Array,
        required: true
    },
})

const chatForm = useForm({
    content: null,
})

const activeChatUser = ref(props.userList.slice(0,1)[0])

function submitMessage () {
    chatForm.post(route('trip.sendMessage', {trip: props.tripId }))
    chatForm.reset()
}

socket.on("message", (data) => {
    const message = JSON.parse(data)
    // state.messageEvents.push(message);

    const msg = {
        id: props.messages.length+1,
        content: message.content,
        user_id: message.user.id
    }
    props.messages.push(msg)
});

onMounted(() => {
    props.messages.sort((a,b) => {
        return (new Date(b.created_at) - new Date(a.created_at));
    }).reverse()
})

</script>

<template>
    <div class="container mx-auto shadow-lg rounded-lg">
        <!-- Chatting -->
        <div class="flex flex-row justify-between bg-white" style="height: 80vh;">
        <!-- chat list -->
        <div class="flex flex-col w-1/5 border-r-2 overflow-y-auto h-full max-h-screen flex-grow">
            <!-- user list -->
            <div @click="activeChatUser=user" v-for="user in userList" class="flex flex-row py-4 px-2 justify-center items-center border-b-2">
                <div class="w-1/4">
                    <img
                    src="https://source.unsplash.com/_7LbC5J-jw4/600x600"
                    class="object-cover h-12 w-12 rounded-full"
                    alt=""
                    />
                </div>
                <div class="w-full">
                    <div class="text-lg font-semibold">{{user.name}}</div>
                    <!-- last msg -->
                    <!-- <span class="text-gray-500">Pick me at 9:00 Am</span> -->
                </div>
            </div>
            <!-- end user list -->
        </div>
        <!-- end chat list -->
        <!-- message -->
        <div class="w-full px-5 flex flex-col justify-between">
            <div class="flex flex-col mt-5 overflow-y-auto">
                <div v-for="msg in messages" :key="msg.id">
                    <div class="flex justify-end mb-4" v-if="msg.user_id === activeChatUser.id">
                        <div class="mr-2 py-3 px-4 bg-blue-600 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-black">
                        {{ msg.content }}
                        </div>
                        <img
                        src="https://source.unsplash.com/vpOeXr5wmR4/600x600"
                        class="object-cover h-8 w-8 rounded-full"
                        alt=""
                        />
                    </div>
                    <div class="flex justify-start mb-4" v-else-if="msg.user_id === sender.id">
                        <img
                            src="https://source.unsplash.com/vpOeXr5wmR4/600x600"
                            class="object-cover h-8 w-8 rounded-full"
                            alt=""
                        />
                        <div class="mr-2 py-3 px-4 bg-gray-400 rounded-br-xl rounded-tl-3xl rounded-tr-xl text-black">
                            {{ msg.content }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t-2 border-gray-200 px-4 pt-4 mb-2 sm:mb-0">
                <div class="relative bottom-0 flex">
                    <input v-model="chatForm.content" type="text" placeholder="Write your message!" class="w-full focus:outline-none focus:placeholder-gray-400 text-gray-600 placeholder-gray-600 pl-12 bg-gray-200 rounded-md py-3">
                    <div class="absolute right-0 items-center inset-y-0 hidden sm:flex">
                        <button @click="submitMessage" type="button" class="inline-flex items-center justify-center rounded-lg px-4 py-3 transition duration-500 ease-in-out text-white bg-blue-500 hover:bg-blue-400 focus:outline-none">
                        <span class="font-bold">Send</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 ml-2 transform rotate-90">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                        </svg>
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>