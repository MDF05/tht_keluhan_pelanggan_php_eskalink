<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Keluhan Pelanggan</h5>
                        <button @click="showAddForm = true" class="btn btn-primary btn-sm">
                            Tambah Keluhan
                        </button>
                    </div>

                    <div class="card-body">
                        <!-- Search Bar -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Cari berdasarkan nama..."
                                        v-model="searchQuery"
                                        @input="searchKeluhan"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" @click="searchKeluhan">
                                        <i class="bi bi-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div v-else-if="keluhanList.length > 0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Nomor HP</th>
                                            <th>Status</th>
                                            <th>Keluhan</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(keluhan, index) in keluhanList" :key="keluhan.id">
											{{ console.log(keluhan) }}
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ keluhan.nama }}</td>
                                            <td>{{ keluhan.email }}</td>
                                            <td>{{ keluhan.nomor_hp }}</td>
                                            <td>
                                                <span :class="getStatusClass(keluhan.status_keluhan)">
                                                    {{ getStatusText(keluhan.status_keluhan) }}
                                                </span>
                                            </td>
                                            <td>{{ keluhan.keluhan }}</td>
                                            <td>{{ formatDate(keluhan.created_at) }}</td>
                                            <td>
                                                <button @click="viewKeluhan(keluhan)" class="btn btn-info btn-sm me-1">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </button>
                                                <button @click="editKeluhan(keluhan)" class="btn btn-warning btn-sm me-1">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <button @click="deleteKeluhan(keluhan.id)" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center">
                            <p class="text-muted">Tidak ada data keluhan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showAddForm" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingKeluhan ? 'Edit' : 'Tambah' }} Keluhan</h5>
                        <button @click="closeModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveKeluhan">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" v-model="formData.nama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" v-model="formData.email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" v-model="formData.nomor_hp" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" v-model="formData.status_keluhan">
                                    <option value="0">Pending</option>
                                    <option value="1">Proses</option>
                                    <option value="2">Selesai</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keluhan</label>
                                <textarea class="form-control" v-model="formData.keluhan" rows="3" required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" @click="closeModal" class="btn btn-secondary me-2">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'



export default {
    setup() {
        const keluhanList = ref([])
        const loading = ref(false)
        const searchQuery = ref('')
        const showAddForm = ref(false)
        const editingKeluhan = ref(null)
        const formData = ref({
            nama: '',
            email: '',
            nomor_hp: '',
            status_keluhan: '0',
            keluhan: ''
        })

        // Fetch data from API
        const fetchKeluhan = async () => {
            loading.value = true
            try {
                const response = await axios.get('/api/keluhan-pelanggan')
                keluhanList.value = response.data
            } catch (error) {
                console.error('Error fetching data:', error)
                alert('Gagal mengambil data keluhan')
            } finally {
                loading.value = false
            }
        }

        // Search keluhan
        const searchKeluhan = async () => {
            if (!searchQuery.value.trim()) {
                await fetchKeluhan()
                return
            }

            loading.value = true
            try {
                const response = await axios.get(`/api/keluhan-pelanggan/search/nama?nama=${searchQuery.value}`)
                keluhanList.value = response.data
            } catch (error) {
                console.error('Error searching:', error)
                alert('Gagal mencari data')
            } finally {
                loading.value = false
            }
        }

        // Save keluhan (create or update)
        const saveKeluhan = async () => {
            try {
                if (editingKeluhan.value) {
                    // Update
                    await axios.put(`/api/keluhan-pelanggan/${editingKeluhan.value.id}`, formData.value)
                } else {
                    // Create
                    await axios.post('/api/keluhan-pelanggan', formData.value)
                }
                
                await fetchKeluhan()
                closeModal()
                alert(editingKeluhan.value ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan')
            } catch (error) {
                console.error('Error saving:', error)
                alert('Gagal menyimpan data')
            }
        }

        // Delete keluhan
        const deleteKeluhan = async (id) => {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return

            try {
                await axios.delete(`/api/keluhan-pelanggan/${id}`)
                await fetchKeluhan()
                alert('Data berhasil dihapus')
            } catch (error) {
                console.error('Error deleting:', error)
                alert('Gagal menghapus data')
            }
        }

        // View keluhan
        const viewKeluhan = (keluhan) => {
            alert(`Detail Keluhan:\nNama: ${keluhan.nama}\nEmail: ${keluhan.email}\nKeluhan: ${keluhan.keluhan}`)
        }

        // Edit keluhan
        const editKeluhan = (keluhan) => {
            editingKeluhan.value = keluhan
            formData.value = { ...keluhan }
            showAddForm.value = true
        }

        // Close modal
        const closeModal = () => {
            showAddForm.value = false
            editingKeluhan.value = null
            formData.value = {
                nama: '',
                email: '',
                nomor_hp: '',
                status_keluhan: '0',
                keluhan: ''
            }
        }

        // Get status class
        const getStatusClass = (status) => {
            const classes = {
                '0': 'badge bg-warning',
                '1': 'badge bg-info',
                '2': 'badge bg-success'
            }
            return classes[status] || 'badge bg-secondary'
        }

        // Get status text
        const getStatusText = (status) => {
            const texts = {
                '0': 'Pending',
                '1': 'Proses',
                '2': 'Selesai'
            }
            return texts[status] || 'Unknown'
        }

        // Format date
        const formatDate = (dateString) => {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }

        // Load data on mount
        onMounted(() => {
            fetchKeluhan()
        })

        return {
            keluhanList,
            loading,
            searchQuery,
            showAddForm,
            editingKeluhan,
            formData,
            fetchKeluhan,
            searchKeluhan,
            saveKeluhan,
            deleteKeluhan,
            viewKeluhan,
            editKeluhan,
            closeModal,
            getStatusClass,
            getStatusText,
            formatDate
        }
    }
}
</script>

<style scoped>
.modal {
    z-index: 1050;
}
</style>