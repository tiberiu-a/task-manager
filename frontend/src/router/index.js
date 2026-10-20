import { createRouter, createWebHistory } from 'vue-router'
import TasksView from '../views/TasksView.vue'
import LoginView from '@/views/LoginView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'tasks',
      component: TasksView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    }    
  ],
})

export default router
