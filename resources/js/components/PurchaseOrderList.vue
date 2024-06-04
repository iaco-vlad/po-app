<template>
    <div class="purchase-order-list">
        <div class="header">
            <h2>Purchase Orders</h2>
            <div class="actions">
                <b-form-input v-model="searchTerm" placeholder="Search..." class="search-input"></b-form-input>
                <b-button variant="primary" @click="navigateToCreate">Create New</b-button>
                <b-button variant="danger" @click="confirmBulkDelete" :disabled="selectedIds.length === 0">Bulk Delete</b-button>
            </div>
        </div>

        <b-table striped hover :items="orders" :fields="fields">
            <template #head(select)>
                <b-form-checkbox v-model="selectAll" @change="onSelectAllChange"/>
            </template>
            <template #cell(select)="row">
                <b-form-checkbox v-model="selectedIds" :value="row.item.id"/>
            </template>
            <template #cell(actions)="row">
                <b-button variant="primary" size="sm" @click="navigateToEdit(row.item.id)">Edit</b-button>
                <b-button variant="danger" size="sm" @click="confirmDelete(row.item.id)">Delete</b-button>
            </template>
        </b-table>

        <b-pagination
            v-model="currentPage"
            :total-rows="pagination.total"
            :per-page="pagination.per_page"
        />

        <div v-if="showDeleteModal" class="delete-modal">
            <div class="delete-modal-content">
                <h3>Confirmation</h3>
                <p v-if="idToDelete">Are you sure you want to delete this purchase order?</p>
                <p v-else>Are you sure you want to delete the selected purchase orders?</p>
                <div class="delete-modal-actions">
                    <button @click="idToDelete ? deleteConfirmed() : bulkDeleteConfirmed()" class="btn-confirm">OK</button>
                    <button @click="cancelDelete" class="btn-cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { BFormCheckbox, BFormInput, BButton, BTable, BPagination } from 'bootstrap-vue-next';

export default {
    name: 'PurchaseOrderList',
    components: {
        BFormCheckbox,
        BFormInput,
        BButton,
        BTable,
        BPagination,
    },
    data() {
        return {
            orders: [],
            searchTerm: '',
            fields: [
                { key: 'select', label: 'Bulk actions' },
                { key: 'purchase_order_number', label: 'PO Number' },
                { key: 'created_at', label: 'Date Received' },
                { key: 'updated_at', label: 'Date Updated' },
                { key: 'buyer_name', label: 'Buyer Name' },
                { key: 'total', label: 'Total' },
                { key: 'actions', label: 'Actions' },
            ],
            pagination: {},
            currentPage: 1,
            idToDelete: null,
            showDeleteModal: false,
            selectedIds: [],
            selectAll: false,
        };
    },
    methods: {
        async fetchOrders(page = null) {
            if (page === null) {
                page = this.pagination.current_page ?? 1;
            }
            try {
                const response = await this.$http.get(`/orders?page=${page}&search=${this.searchTerm}`);
                this.orders = response.data.data;
                this.pagination = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        navigateToCreate() {
            this.$router.push({ name: 'purchase-order-create' });
        },
        navigateToEdit(id) {
            this.$router.push({ name: 'purchase-order-edit', params: { id } });
        },
        async confirmDelete(id) {
            this.idToDelete = id;
        },
        async deleteConfirmed() {
            try {
                await this.$http.delete(`/orders/${this.idToDelete}`);
                await this.fetchOrders();
                this.showDeleteModal = false;
                this.idToDelete = null;
            } catch (error) {
                console.error(error);
            }
        },
        cancelDelete() {
            this.showDeleteModal = false;
            this.idToDelete = null;
        },

        onSelectAllChange() {
            this.selectedIds = this.selectAll ? this.orders.map(order => order.id) : [];
        },
        async confirmBulkDelete() {
            this.idToDelete = null;
            this.showDeleteModal = true;
        },
        async bulkDeleteConfirmed() {
            try {
                await this.$http.delete('/orders/bulk', { data: { ids: this.selectedIds } });
                await this.fetchOrders();
                this.showDeleteModal = false;
                this.selectedIds = [];
                this.selectAll = false;
            } catch (error) {
                console.error(error);
            }
        },
    },
    watch: {
        currentPage() {
            this.fetchOrders(this.currentPage);
        },
        searchTerm() {
            this.fetchOrders();
        },
        idToDelete(newValue) {
            if (newValue !== null) {
                this.showDeleteModal = true;
            }
        },
    },
    async mounted() {
        await this.fetchOrders();
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

.search-input {
    max-width: 300px;
}


.delete-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.delete-modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    max-width: 400px;
    width: 100%;
}

.delete-modal-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-confirm,
.btn-cancel {
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-confirm {
    background-color: #dc3545;
    color: white;
    margin-right: 10px;
}

.btn-cancel {
    background-color: #6c757d;
    color: white;
}
</style>
