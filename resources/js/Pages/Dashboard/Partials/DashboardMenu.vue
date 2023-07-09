<script>
import IconWTooltip from '@/Components/IconWTooltip.vue';
import { useForm } from '@inertiajs/vue3';
import draggable from 'vuedraggable'


export default {
    name: "DashboardMenu",
    props: {
        editMode: Boolean,
        default: false,
        lists: Array,
        currentListId: Number,
    },
    components: {
        IconWTooltip,
        draggable,
    },
    data() {
        return {
            drag: false,
            formCreateList: useForm({
                name: '',
            }),
            menuLists: this.lists,
        }
    },
    methods: {
        setActive(clickedList) {
            if (this.editMode) return;
            this.$emit('getActiveList', clickedList.id);
        },
        addList() {
            this.$emit('createList');
        }
    },
    watch: {
        lists() {
            this.menuLists = this.lists;
        }
    }
    }
</script>

<template>
    <div class="px-8 flex flex-row w-full items-center">
        <draggable v-model="menuLists" class="flex flex-row w-5/6" item-key="id" :disabled="!editMode">
            <template #item="{ element }">
                <div :class="[{'border-b-2 border-indigo-400' : element.id === currentListId}, [editMode ? 'cursor-move' : 'cursor-pointer']]" class="list-menu-item" @click="setActive(element)">
                    {{ element.name }}
                </div>
            </template>
        </draggable>
        <div v-if="editMode === true && lists.length < 5" class="w-1/6">
            <div class="border-2 border-dashed rounded w-full py-2 flex justify-center border-gray-300">
                <icon-w-tooltip iconType="plus" tooltipmsg="Add new list" @click="addList"></icon-w-tooltip>
            </div>
        </div>
    </div>
</template>
