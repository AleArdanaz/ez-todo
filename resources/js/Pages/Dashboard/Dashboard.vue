<script>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ToDoList from '@/Components/ToDoList.vue';
import DashboardMenu from './Partials/DashboardMenu.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';


export default {
    props: {
        lists: Array,
    },
    components: {
        AuthenticatedLayout,
        Head,
        ToDoList,
        DashboardMenu,
    },
    data() {
        return {
            showAlert: false,
            alertResponse: '',
            alertStyle: 'success',
            onEditMode: false,
            currentList: {},
            mutableLists: [...this.lists],
            form: useForm({
                name: '',
            }),
        };
    },
    methods: {
        setTaskDone(task) {
            axios.put(route('tasks.completed', task.id), {
                completed: task.completed,
            })
            .then(response => {
                this.showAndHideAlert(response.data.message);
            })
            .catch(error => {
                this.showAndHideAlert(error.response.data.message);
            });
        },
        showAndHideAlert(positive, response) {
            this.alertStyle = positive ? 'success' : 'error';
            this.alertResponse = response;
            this.showAlert = true;
            setTimeout(() => {
                this.showAlert = false;
            }, 2000);
        },
        setEditMode(value, list = null) {
            this.onEditMode = value;
            if (value === false) {
                this.updateTasks(list);
            }
        },
        selectList(id) {
            this.currentList = this.mutableLists.find(list => list.id === id);
        },
        createList() {
            axios.post(route('list.store')).then(response => {
                this.showAndHideAlert(true,response.data.message)
                this.mutableLists.push(response.data.list);
                this.currentList = response.data.list;
            }).catch(error => {
                this.showAndHideAlert(false, error.response.data.message)
            });
        },
        deleteList(listId) {
            if (this.mutableLists.length === 1) {
                this.showAndHideAlert(false, 'You cannot delete the last list');
                return;
            }

            axios.delete(route('list.destroy', listId))
            .then(response => {
                this.showAndHideAlert(true,response.data.message)
                this.mutableLists = this.mutableLists.filter(list => list.id !== listId);
                this.currentList = this.mutableLists[0];
            }).catch(error => {
                console.log('entra aca 2');
                this.showAndHideAlert(false, error.response.data.message)
            });
        },
        updateTasks(list) {
            axios.post(route('list.update', this.currentList.id), {
                list: list,
            }).then(response => {
                this.showAndHideAlert(true,response.data.message)
            }).catch(error => {
                this.showAndHideAlert(false, error.response.data.message)
            });
        },
    },
    mounted() {
        this.currentList = this.mutableLists[0];
    },
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <DashboardMenu
            :edit-mode="onEditMode"
            :lists="mutableLists"
            :currentListId="currentList.id"
            @get-active-list="selectList"
            @create-list="createList"
            />
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="w-full">
                    <ToDoList
                    :list="currentList"
                    @set-task-done="setTaskDone"
                    @show-alert="showAndHideAlert"
                    @edit-mode="setEditMode"
                    @delete-list="deleteList"
                    />
                </div>
            </div>
        </div>
        <div :class="['alert', { show: showAlert }, alertStyle]">
            <p>{{ alertResponse }}</p>
        </div>
    </AuthenticatedLayout>
</template>
