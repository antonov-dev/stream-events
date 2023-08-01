<script>
import {defineComponent} from 'vue'
import axios from "axios";
import intersection from '@/src/directives/Intersection.js'

export default defineComponent({
    name: "EventsList",
    directives: { intersection },
    data() {
        return {
            events: [],
            lastTimestamps: [],
            isLoading: true,
            last: 0,
            limit: 100,
            colors: {
                donation: '#007bff',
                subscriber: '#e83e8c',
                follower: '#6f42c1',
                merch_sale: '#5bbd45',
            }
        }
    },
    methods: {
        async getEvents() {
            await axios
                .get(window.location.origin + import.meta.env.VITE_API_URL +'/events', {
                    params: {
                        last: this.last,
                        limit: this.limit,
                    }
                })
                .then((response) => {
                    if(response.data.status === 'success' && response.data.data) {
                        this.events = [...this.events, ...response.data.data];
                        this.last = response.data.data.pop().created_timestamp;
                        this.isLoading = false;
                    }
                })
                .catch((e) => {
                    console.log(e);
                })
        },
        async toggleRead(event) {
            await axios
                .patch(window.location.origin + import.meta.env.VITE_API_URL +'/events/' + event.id, {
                    'is_read': !event.is_read
                })
                .then((response) => {
                    if(response.data.status === 'success') {
                        event.is_read = !event.is_read;
                    }
                })
                .catch((e) => {
                    console.log(e);
                })
        }
    },
    mounted() {
        this.getEvents();

        // Try to load one more time if DB seeding will take to much
        setTimeout(() => {
            if(this.events.length === 0) {
                this.getEvents();
            }
        }, 15000);
    }
})
</script>

<template>
    <div class="my-5">
        <h6 class="border-bottom pb-1 mb-3">Events</h6>

        <div v-if="!isLoading" v-for="event in events">
            <div class="d-flex text-muted">
                <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" :fill="colors[event.type]"></rect><text x="50%" y="50%" :fill="colors[event.type]" dy=".3em">32x32</text></svg>

                <div class="pb-3 mb-0 small lh-sm w-100">
                    <div class="d-flex justify-content-between">
                        <strong class="d-block text-gray-dark">{{event.id}} | {{ event.type }} | {{ event.created_at }}</strong>
                        <a href="#" @click.prevent="toggleRead(event)">{{ event.is_read ? 'Unread' : 'Read'}}</a>
                    </div>
                    <div v-if="event.type === 'donation'">RandomUser donated {{ event.eventable.amount + ' ' + event.eventable.currency }} to you! | "{{event.eventable.message}}"</div>
                    <div v-else-if="event.type === 'subscriber'">RandomUser ({{ event.eventable.tier }}) subscribed to you!</div>
                    <div v-else-if="event.type === 'follower'">RandomUser followed to you!</div>
                    <div v-else-if="event.type === 'merch_sale'">RandomUser bought {{ event.eventable.amount }} fancy pants from you for {{ event.eventable.price + ' ' + event.eventable.currency }}</div>
                </div>
            </div>
        </div>

        <div v-else class="d-flex align-items-center mt-4 justify-content-center">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="ms-3">Seeding in progress...</div>
        </div>

        <div v-if="!isLoading" v-intersection="getEvents"></div>
    </div>
</template>

<style scoped>

</style>
