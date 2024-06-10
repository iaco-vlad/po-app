import { createRouter, createWebHistory } from 'vue-router';
import PurchaseOrderList from './components/PurchaseOrderList.vue';
import PurchaseOrderForm from './components/PurchaseOrderForm.vue';
import PurchaseOrderMetrics from "./components/PurchaseOrderMetrics.vue";

const routes = [
    {
        path: '/',
        name: 'purchase-order-list',
        component: PurchaseOrderList,
    },
    {
        path: '/orders/create',
        name: 'purchase-order-create',
        component: PurchaseOrderForm,
    },
    {
        path: '/orders/:id/edit',
        name: 'purchase-order-edit',
        component: PurchaseOrderForm,
        props: true,
    },
    {
        path: '/metrics',
        name: 'purchase-order-metrics',
        component: PurchaseOrderMetrics,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
