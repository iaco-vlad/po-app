<template>
    <div class="purchase-order-create">
        <h2>Create Purchase Order</h2>
        <b-form @submit.prevent="createPurchaseOrder">
            <b-form-group label="PO Number" label-for="po-number">
                <b-form-input id="po-number" v-model="purchaseOrder.purchase_order_number" required/>
            </b-form-group>

            <b-form-group label="Buyer Name" label-for="buyer-name">
                <b-form-input id="buyer-name" v-model="purchaseOrder.buyer_name" required/>
            </b-form-group>

            <b-form-group label="Total" label-for="total">
                <b-form-input id="total" v-model="purchaseOrder.total" type="number" step="0.01" required/>
            </b-form-group>

            <b-button type="submit" variant="primary">Create</b-button>
            <b-button type="button" variant="secondary" @click="navigateBack">Cancel</b-button>
        </b-form>
    </div>
</template>

<script>
import { BForm, BFormGroup, BFormInput, BButton } from 'bootstrap-vue-next';

export default {
    name: 'PurchaseOrderCreate',
    components: {
        BForm,
        BFormGroup,
        BFormInput,
        BButton,
    },
    data() {
        return {
            purchaseOrder: {
                purchase_order_number: '',
                buyer_name: '',
                total: 0,
            },
        };
    },
    methods: {
        async createPurchaseOrder() {
            try {
                const response = await this.$http.post('/orders', this.purchaseOrder);
                const createdPurchaseOrder = response.data;
                this.navigateToEdit(createdPurchaseOrder.id);
            } catch (error) {
                console.error(error);
            }
        },
        navigateBack() {
            this.$router.push({ name: 'purchase-order-list' });
        },
        navigateToEdit(id) {
            this.$router.push({ name: 'purchase-order-edit', params: { id } });
        },
    },
};
</script>

<style scoped>
.purchase-order-create {
    padding: 20px;
}
</style>
