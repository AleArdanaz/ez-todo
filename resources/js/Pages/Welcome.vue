<script>
import { Head, Link } from '@inertiajs/vue3';
import { reactive } from 'vue';
export default {
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
    },
    components: {
        Head,
        Link,
    },
    data() {
        return {
            items: ['● Simple ToDo list', '● No visual contamination', '● Just get things done', "● Free to use"],
            typedItems: reactive([]),
            typingIndex: 0,
            typingInterval: null,
            isTyping: false,
        };
    },
    mounted() {
        this.startTyping();
    },
    methods: {
        startTyping() {
            this.typingInterval = setInterval(() => {
                if (this.typingIndex >= this.items.length) {
                    clearInterval(this.typingInterval);
                    return;
                }

                if (this.isTyping) return;

                const span = this.$refs['itemText-' + this.typingIndex][0];
                const text = this.items[this.typingIndex];
                this.typeText(span, text, this.typingIndex);

                this.typingIndex++;
            }, 750); // Adjust the delay between items as needed
        },
        typeText(span, text, index) {
            let charIndex = 0;
            this.isTyping = true;
            const typingInterval = setInterval(() => {
                if (charIndex <= text.length) {
                    this.typedItems[index] = text.substr(0, charIndex);
                    charIndex++;
                } else {
                    this.isTyping = false;
                    clearInterval(typingInterval);
                }
            }, 50); // Adjust the typing speed as needed
        },
    },
}
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex items-center justify-center lg:justify-start min-h-screen bg-center bg-slate-900">
        <div v-if="canLogin" class="fixed top-0 right-0 p-6 text-right">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link>
            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-100 hover:text-gray-300 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500 mr-6"
                    >Log in</Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 font-semibold text-gray-100 hover:text-gray-300 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link>
            </template>
        </div>
        <div>
            <ul class="font-semibold lg:text-4xl text-gray-300 lg:ml-28 text-3xl">
                <li v-for="(index) in items" :key="index" class="mb-6">
                    <span :ref="'itemText-' + index" class="typed-text">{{ typedItems[index] }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<style>
.typed-text {
    opacity: 0;
    animation: typing-animation 1s linear forwards;
}

@keyframes typing-animation {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
</style>
