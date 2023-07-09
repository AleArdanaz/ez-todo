<script>
import draggable from 'vuedraggable'
import IconWTooltip from '@/Components/IconWTooltip.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

export default {
    name: "ToDoList",
    props: {
        msg: String,
        list: Object,
    },
    components: {
        draggable,
        IconWTooltip,
        Modal,
        DangerButton,
        SecondaryButton,
    },
    data() {
        return {
            editList: false,
            drag: false,
            showConfirmationDeleteList: false,
            modifiedTasks: [],
            timer: null,
        }},
        methods: {
        getClass(done) {
            return !done ? 'fill-current text-green-500' : 'fill-current-done text-gray-300'
        },
        setTaskToDone(task) {

            if (this.editList) return;

            task.completed = !task.completed

            if (!this.modifiedTasks.includes(task)) {
                this.modifiedTasks.push(task.id);
            }

            clearTimeout(this.timer);

            this.timer = setTimeout(() => {
                this.sendBatchRequest();
            }, 2000);
        },
        sendBatchRequest() {
            axios.post(route('tasks.complete'), {
                tasks: this.modifiedTasks,
            })
            .catch(error => {
                this.$emit('show-alert', error.response.data.message);
            });
        },
        deleteTask(task) {
            this.list.tasks.splice(this.list.tasks.indexOf(task), 1)
        },
        addTask() {
            const newTask = {
                id: Date.now(),
                name: '',
                done: false,
                order: this.list.tasks.length + 1
            }
            this.list.tasks.push(newTask)
            this.$nextTick(() => {
                this.focusTaskEdit(newTask.id)
            })
        },
        focusTaskEdit(id) {
            let task_id = 'task-' + id
            let el = document.getElementById(task_id);
            el.focus();
            this.placeCursorAtEnd(el);
        },
        placeCursorAtEnd(el) {
            el.selectionStart = el.selectionEnd = el.value.length;
            el.focus();
        },
        toggleAlert(response) {
                this.showConfirmationDeleteList = true;
        },
        closeModal() {
            this.showConfirmationDeleteList = false;
        },
        deleteList() {
            this.$emit('delete-list', this.list.id);
            this.showConfirmationDeleteList = false;
        },
        editMode() {
            if (this.list.tasks.length > 9 && this.editList) {
                this.$emit('show-alert', false, 'You can only have 9 tasks per list.');
                return;
            }
            this.editList = !this.editList;
            if (this.editList === false) {
                this.$emit('edit-mode', this.editList, this.list);
            } else {
                this.$emit('edit-mode', this.editList);
            }
        },
        updateOrder() {
            this.list.tasks.forEach((task, index) => {
                task.order = index + 1;
            });
        },
        handleEnterKey(event) {
            this.addTask();
        },
        handleKeyboardShortcuts(event) {
            if (event.ctrlKey && event.key === 's') {
                event.preventDefault();
                this.editMode();
            }

            if ((event.ctrlKey && event.key === 'a') && this.editList) {
                event.preventDefault();
                this.addTask();
            }
        },
        },
        mounted() {
            window.addEventListener('keydown', this.handleKeyboardShortcuts);
        }
}

</script>

<template>
    <div class="font-sans w-full flex justify-center items-center relative">
        <div class="p-6 rounded w-full lg:w-1/2 xl:w-full">
            <!-- List Header -->
            <div class="flex flex-row items-center justify-between">
                <h1 v-if="!editList" class="font-medium text-3xl text-gray-800 dark:text-gray-100"> {{ list.name }} </h1>
                <input class="rounded bg-gray-200 text-gray-600 dark:bg-slate-500 dark:text-gray-100" :id="'list-' + list.id" v-if="editList" type="text" v-model="list.name">
                <div class="flex items-center">
                    <icon-w-tooltip v-if="!editList" iconType="lock" tooltipmsg="Edit" @click="editMode" />
                    <icon-w-tooltip class="animate-pulse button-scale-animation" v-if="editList" iconType="lock-open" tooltipmsg="Save changes" @click="editMode" />
                </div>
            </div>
            <div class="border-2 border-gray-300 my-5"></div>
            <!-- To Do List -->
            <div v-if="list.tasks">
                <draggable v-model="list.tasks" class="w-full" item-key="id" @end="updateOrder" :disabled="!editList" handle=".handle">
                    <template #item="{element}">
                        <div class="duration-200 py-2 mr-5 font-normal text-xl flex items-center border-2 rounded my-3 p-5 w-full hover:bg-gray-200 z-10 dark:hover:bg-slate-600">
                            <div class="flex flex-row items-center w-1/2 justify-start">
                                <!-- Check Icon -->
                                <div v-if="!editList">
                                    <svg @click="setTaskToDone(element)" :class="getClass(element.completed)"
                                         xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 icon" viewBox="0 0 24 24">
                                        <path
                                            d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                            stroke="#323232" stroke-width="2" />
                                        <path v-if="element.completed" d="M5.5 12.5L9.5 16.5L18.5 7.5" stroke="#008000"
                                              stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="mr-3 cursor-move handle p-2" v-if="editList">
                                    <icon-w-tooltip iconType="bars"/>
                                </div>
                                <!-- Task Name -->
                                <div class="task">
                                    <span
                                        v-if="!editList"
                                        @click="setTaskToDone(element)"
                                        :class="[{ 'done': element.completed }, {'task-editable': editList}]"
                                        class="hover:text-gray-800 text-gray-800 dark:text-gray-100 font-medium task-text"
                                        :contenteditable="editList">{{ element.name }}
                                    </span>
                                    <input class="rounded bg-gray-200 text-gray-600 dark:bg-slate-500 dark:text-gray-100" :id="'task-' + element.id" v-if="editList" type="text" v-model="element.name" @keydown.enter="handleEnterKey(index)">
                                </div>
                            </div>
                            <div class="w-1/4 flex justify-center">
                                <p v-show="element.order <= 2">🔥</p>
                                <p v-show="element.order <= 5 && element.order > 2">👀</p>
                                <p v-show="element.order <= 8 && element.order > 5">😎</p>
                            </div>
                            <!-- Actions -->
                            <div v-show="editList" class="actions flex flex-row w-1/4 justify-end items-center">
                                <icon-w-tooltip iconType="pen-to-square" tooltipmsg="Edit" class="mr-2" @click="focusTaskEdit(element.id)"/>
                                <icon-w-tooltip iconType="trash" tooltipmsg="Delete" @click="deleteTask(element)"/>
                            </div>
                        </div>
                    </template>
                </draggable>
                <!-- Add new task -->
                <div>
                    <div v-show="editList" class="flex flex-row justify-between items-center">
                        <div class="border-2 border-dashed rounded w-full py-2 flex justify-center border-gray-300">
                            <icon-w-tooltip iconType="plus" @click="addTask()"></icon-w-tooltip>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="editList" @click="toggleAlert('success')" class="absolute text-red-500 border-dotted border-red-500 border-2 rounded px-6 py-3 cursor-pointer delete-list">
            <p>Delete</p>
        </div>
        <Modal :show="showConfirmationDeleteList" @close="closeModal">
            <div class="p-6 dark:bg-slate-700 dark:border-slate-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete this list?
                </h2>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        @click="deleteList"
                    >
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
