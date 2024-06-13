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

            <h3 class="mt-3">Items</h3>

            <div v-for="(item, index) in purchaseOrder.items" :key="index" class="d-flex justify-content-around">
                <div class="item-field">
                    <b-form-group :label="'Item ' + (index + 1)" label-for="item-description">
                        <b-form-input :id="'item-description-' + index" v-model="item.description" required/>
                    </b-form-group>
                </div>
                <div class="item-field">
                    <b-form-group label="Quantity" label-for="item-quantity">
                        <b-form-input :id="'item-quantity-' + index" v-model="item.quantity" type="number" min="1" required/>
                    </b-form-group>
                </div>
                <div class="item-field">
                    <b-form-group label="Unit Price" label-for="item-unit-price">
                        <b-form-input :id="'item-unit-price-' + index" v-model="item.unit_price" type="number" step="0.01" min="0" required/>
                    </b-form-group>
                </div>
                <div class="item-field">
                    <b-form-group label="Category" label-for="item-category">
                        <b-form-select :id="'item-category-' + index" v-model="item.category" :options="categoryOptions" required/>
                    </b-form-group>
                </div>
                <div class="item-field">
                    <b-button v-if="index !== 0" variant="danger" @click="removeItem(index)" class="mt-4">Remove Item</b-button>
                </div>
            </div>

            <br>

            <div v-if="itemsError" class="error-message">Please add at least one item to the purchase order.</div>

            <b-button variant="secondary" @click="addItem">Add Item</b-button>
            <b-button type="submit" variant="primary" class="mx-2" :disabled="!isFormValid">{{ isEdit ? 'Update' : 'Create' }}</b-button>
            <b-button type="button" variant="secondary" @click="navigateBack">Cancel</b-button>
        </b-form>
    </div>
</template>

<script>
import { BForm, BFormGroup, BFormInput, BFormSelect, BButton } from 'bootstrap-vue-next';

export default {
    name: 'PurchaseOrderForm',
    components: {
        BForm,
        BFormGroup,
        BFormInput,
        BFormSelect,
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
                items: [
                    {
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        category: 'Category 1',
                    },
                ],
            },
            isEdit: false,
            itemsError: false,
            categoryOptions: ['Category 1', 'Category 2', 'Category 3'],
        };
    },
    computed: {
        isFormValid() {
            return (
                this.purchaseOrder.purchase_order_number &&
                this.purchaseOrder.buyer_name &&
                this.purchaseOrder.items.every(item => item.description && item.quantity && item.unit_price && item.category)
            );
        },
        isEdit() {
            return this.id !== null;
        }
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
            if (!this.isFormValid) {
                return;
            }
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
                category: 'Category 1',
            });
        },
        removeItem(index) {
            if (index !== 0) {
                this.purchaseOrder.items.splice(index, 1);
            }
        },
    },
    mounted() {
        if (this.id) {
            this.fetchPurchaseOrder();
        }
    },
};
</script>

<style scoped>
.purchase-order-form {
    padding: 20px;
}
.error-message {
    color: red;
    margin-bottom: 10px;
}
.item-field {
    flex: 1;
    margin-right: 10px;
}
.item-field:last-child {
    margin-right: 0;
}
</style>
