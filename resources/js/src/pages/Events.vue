<script>
import {defineComponent} from 'vue'
import EventsList from "@/src/components/EventsList.vue";
import StatsPanel from "@/src/components/StatsPanel.vue";

export default defineComponent({
    name: "Events",
    components: {StatsPanel, EventsList}
})
</script>

<template>
    <StatsPanel />

    <EventsList />
</template>

<style scoped>

</style>
