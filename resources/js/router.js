import { createRouter, createWebHistory } from 'vue-router';
import HomeView from './views/HomeView.vue';
import MatchDetailsView from './views/MatchDetailsView.vue';

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'home', component: HomeView },
        { path: '/matches/:id', name: 'match-details', component: MatchDetailsView, props: true },
    ],
});
