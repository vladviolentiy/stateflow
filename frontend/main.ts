import { createApp } from 'vue'
import { createPinia } from 'pinia'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-icons/font/bootstrap-icons.scss'
import App from './App.vue'

import { createRouter, createWebHashHistory } from 'vue-router'

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/register',
      component: () => import('./components/Auth/RegisterPage.vue')
    },
    {
      path: '/auth',
      component: () => import('./components/Auth/AuthPage.vue')
    },
    {
      path: '/dashboard',
      component: () => import('./components/DashboardComponent.vue'),
      children: [
        {
          path: '',
          component: () => import('./components/Dashboard/HomeDashboard.vue')
        },
        {
          path: 'profile',
          component: () => import('./components/EmptyRouterView.vue'),
          children: [
            {
              path: '',
              component: () => import('./components/Dashboard/ProfileDashboard.vue')
            },
            {
              path: 'email',
              component: () => import('./components/Dashboard/Profile/EmailConfigure.vue')
            },
            {
              path: 'phones',
              component: () => import('./components/Dashboard/Profile/PhonesConfigure.vue')
            },
            {
              path: 'sessions',
              component: () => import('./components/Dashboard/Profile/SessionConfigure.vue')
            }
          ]
        },
        {
          path: 'services',
          component: () => import('./components/Dashboard/ServicesDashboard.vue')
        }
      ]
    }
  ]
})

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
