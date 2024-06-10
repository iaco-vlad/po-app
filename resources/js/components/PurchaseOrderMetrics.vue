<template>
    <div class="purchase-order-list">
        <div class="header">
            <h2>Purchase Orders Metrics</h2>

            <div class="actions">
                <b-button variant="info" @click="$router.push({ name: 'purchase-order-list' })">Return to the List</b-button>
            </div>
        </div>

        <highcharts :options="chartOptions"></highcharts>
        <highcharts :options="categoryChartOptions"></highcharts>
    </div>
</template>

<script>
import { Chart } from 'highcharts-vue';
import { BButton } from "bootstrap-vue-next";

export default {
    name: 'PurchaseOrderMetrics',
    components: {
        highcharts: Chart,
        BButton,
    },
    data() {
        return {
            chartOptions: {
                chart: {
                    type: 'column',
                },
                title: {
                    text: 'Purchase Orders Received per Day',
                },
                xAxis: {
                    type: 'category',
                },
                yAxis: {
                    title: {
                        text: 'Number of Purchase Orders',
                    },
                },
                series: [
                    {
                        name: 'Purchase Orders',
                        data: [],
                    },
                ],
            },
            categoryChartOptions: {
                chart: {
                    type: 'pie',
                },
                title: {
                    text: 'Number of Items per Category',
                },
                series: [
                    {
                        name: 'Items',
                        data: [],
                    },
                ],
            },
        };
    },
    methods: {
        async fetchPurchaseOrderMetrics() {
            try {
                const response = await this.$http.get('/metrics/purchase-orders');
                const {purchaseOrdersPerDay, itemsPerCategory} = response.data;

                this.chartOptions.xAxis.categories = Object.keys(purchaseOrdersPerDay);
                this.chartOptions.series[0].data = Object.entries(purchaseOrdersPerDay).map(
                    ([date, count]) => ({name: date, y: count})
                );

                this.categoryChartOptions.series[0].data = Object.entries(itemsPerCategory).map(
                    ([category, count]) => ({
                        name: category,
                        y: count,
                    })
                );
            } catch (error) {
                console.error(error);
            }
        },
    },
    mounted() {
        this.fetchPurchaseOrderMetrics();
    },
};
</script>

<style scoped>
.purchase-order-list {
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
</style>
