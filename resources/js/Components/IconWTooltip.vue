<script>
import { ref, computed } from 'vue';

export default {
    name: "IconWTooltip",
    props: {
        iconType: {
            type: String,
            required: true,
        },
        tooltipmsg: {
            type: String,
        },
    },
    computed: {
        pointer() {
            return this.iconType === 'bars' ? 'cursor-move' : 'cursor-pointer';
        }
    }
}
</script>

<template>
    <div class="tooltip flex items-center justify-center">
        <font-awesome-icon class="h-4 w-4 dark:text-gray-100 dark:hover:text-gray-300 transition ease-in-out duration-150" :icon="iconType" :class="pointer"/>
        <div v-if="tooltipmsg" class="tooltiptext z-50">{{ tooltipmsg }}</div>
    </div>
</template>
