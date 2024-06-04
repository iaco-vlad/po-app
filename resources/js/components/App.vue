<template>
    <div class="container">
        <router-view></router-view>
    </div>
</template>

<script>
import PurchaseOrderList from './PurchaseOrderList.vue';

export default {
    name: "App",

    components: {
        PurchaseOrderList,
    },
};
</script>
