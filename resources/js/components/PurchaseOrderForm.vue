<template>
    <div class="purchase-order-form">
        <h2>{{ isEdit ? 'Edit Purchase Order' : 'Create Purchase Order' }}</h2>
        <b-form @submit.prevent="submitForm">
            <b-form-group label="PO Number" label-for="po-number">
                <b-form-input id="po-number" v-model="purchaseOrder.purchase_order_number" required/>
            </b-form-group>

            <b-form-group label="Buyer Name" label-for="buyer-name">
                <b-form-input id="buyer-name" v-model="purchaseOrder.buyer_name" required/>
            </b-form-group>

            <b-form-group label="Total" label-for="total">
                <b-form-input id="total" v-model="purchaseOrder.total" type="number" step="0.01" required/>
            </b-form-group>

            <div v-for="(item, index) in purchaseOrder.items" :key="index" class="d-flex justify-content-around">
                <b-button variant="danger" @click="removeItem(index)" class="mt-4">Remove Item</b-button>
                <b-form-group :label="'Item ' + (index + 1)" label-for="item-description">
                    <b-form-input :id="'item-description-' + index" v-model="item.description" required/>
                </b-form-group>
                <b-form-group label="Quantity" label-for="item-quantity">
                    <b-form-input :id="'item-quantity-' + index" v-model="item.quantity" type="number" required/>
                </b-form-group>
                <b-form-group label="Unit Price" label-for="item-unit-price">
                    <b-form-input :id="'item-unit-price-' + index" v-model="item.unit_price" type="number" step="0.01" required/>
                </b-form-group>
                <b-form-group label="Category" label-for="item-category">
                    <b-form-input :id="'item-category-' + index" v-model="item.category" required/>
                </b-form-group>
            </div>

            <br>

            <b-button variant="secondary" @click="addItem">Add Item</b-button>
            <b-button type="submit" variant="primary" class="mx-2">{{ isEdit ? 'Update' : 'Create' }}</b-button>
            <b-button type="button" variant="secondary" @click="navigateBack">Cancel</b-button>
        </b-form>
    </div>
</template>

<script>
import { BForm, BFormGroup, BFormInput, BButton } from 'bootstrap-vue-next';

export default {
    name: 'PurchaseOrderForm',
    components: {
        BForm,
        BFormGroup,
        BFormInput,
        BButton,
    },
    props: {
        id: {
            type: [Number, String],
            default: null,
        },
    },
    data() {
        return {
            purchaseOrder: {
                purchase_order_number: '',
                buyer_name: '',
                total: 0,
                items: [],
            },
            isEdit: false,
        };
    },
    methods: {
        async fetchPurchaseOrder() {
            try {
                const response = await this.$http.get(`/orders/${this.id}`);
                this.purchaseOrder = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async submitForm() {
            try {
                if (this.isEdit) {
                    await this.$http.put(`/orders/${this.id}`, this.purchaseOrder);
                } else {
                    await this.$http.post('/orders', this.purchaseOrder);
                }
                this.navigateBack();
            } catch (error) {
                console.error(error);
            }
        },
        navigateBack() {
            this.$router.push({ name: 'purchase-order-list' });
        },
        addItem() {
            this.purchaseOrder.items.push({
                description: '',
                quantity: 1,
                unit_price: 0,
                category: '',
            });
        },
        removeItem(index) {
            this.purchaseOrder.items.splice(index, 1);
        },
    },
    mounted() {
        if (this.id) {
            this.isEdit = true;
            this.fetchPurchaseOrder();
        }
    },
};
</script>

<style scoped>
.purchase-order-form {
    padding: 20px;
}
</style>
