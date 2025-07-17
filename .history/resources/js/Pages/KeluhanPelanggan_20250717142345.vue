<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Keluhan Pelanggan</h5>
                        <button @click="showAddForm = true" class="btn btn-primary btn-sm">
                            Tambah Keluhan
                        </button>
                    </div>

                    <div class="card-body">
                       
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
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <button class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#" @click.prevent="exportData('csv')">Export CSV</a></li>
                                        <li><a class="dropdown-item" href="#" @click.prevent="exportData('xls')">Export XLS</a></li>
                                        <li><a class="dropdown-item" href="#" @click.prevent="exportData('txt')">Export TXT</a></li>
                                        <li><a class="dropdown-item" href="#" @click.prevent="exportData('pdf')">Export PDF</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        
                        <div v-if="loading" class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        
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
                                            <td class="d-flex">
                                                <button @click="viewKeluhan(keluhan)" class="btn btn-info btn-sm me-1">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </button>
                                                <button @click="editKeluhan(keluhan)" class="btn btn-warning btn-sm me-1 ">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <button @click="deleteKeluhan(keluhan.id)" class="btn btn-danger btn-sm me-1 ">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                                <button @click="showHistory(keluhan)" class="btn btn-secondary btn-sm">
                                                    <i class="bi bi-clock-history"></i> History
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        
                        <div v-else class="text-center">
                            <p class="text-muted">Tidak ada data keluhan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
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
                                <div v-if="formErrors.nama" class="text-danger small">{{ formErrors.nama }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" v-model="formData.email" required>
                                <div v-if="formErrors.email" class="text-danger small">{{ formErrors.email }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" v-model="formData.nomor_hp" required>
                                <div v-if="formErrors.nomor_hp" class="text-danger small">{{ formErrors.nomor_hp }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" v-model="formData.status_keluhan">
                                    <option value="0">Pending</option>
                                    <option value="1">Proses</option>
                                    <option value="2">Selesai</option>
                                </select>
                                <div v-if="formErrors.status_keluhan" class="text-danger small">{{ formErrors.status_keluhan }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keluhan</label>
                                <textarea class="form-control" v-model="formData.keluhan" rows="3" required></textarea>
                                <div v-if="formErrors.keluhan" class="text-danger small">{{ formErrors.keluhan }}</div>
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

        
        <div v-if="showHistoryModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Timeline Status Keluhan</h5>
                        <button @click="closeHistoryModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="historyLoading" class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div v-else>
                            <div v-if="keluhanHistory.length > 0">
                                <ul class="timeline list-unstyled">
                                    <li v-for="(item) in keluhanHistory" :key="item.id" class="mb-3">
                                        <div>
                                            <span :class="getStatusClass(item.status_keluhan)">
                                                {{ getStatusText(item.status_keluhan) }}
                                            </span>
                                            <small class="text-muted ms-2">{{ formatDate(item.updated_at) }}</small>
                                        </div>
                                        <div class="ms-3 mt-1">
                                            <strong>Keluhan:</strong> {{ selectedKeluhan?.keluhan }}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="text-center text-muted">Tidak ada history status</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ! Modal Lihat Keluhan -->
        <div v-if="showViewModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Keluhan</h5>
                        <button @click="closeViewModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr><th>Nama</th><td>{{ selectedViewKeluhan?.nama }}</td></tr>
                                <tr><th>Email</th><td>{{ selectedViewKeluhan?.email }}</td></tr>
                                <tr><th>Nomor HP</th><td>{{ selectedViewKeluhan?.nomor_hp }}</td></tr>
                                <tr><th>Status</th><td><span :class="getStatusClass(selectedViewKeluhan?.status_keluhan)">{{ getStatusText(selectedViewKeluhan?.status_keluhan) }}</span></td></tr>
                                <tr><th>Keluhan</th><td>{{ selectedViewKeluhan?.keluhan }}</td></tr>
                                <tr><th>Tanggal</th><td>{{ formatDate(selectedViewKeluhan?.created_at) }}</td></tr>
                            </tbody>
                        </table>
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
        const showHistoryModal = ref(false)
        const keluhanHistory = ref([])
        const historyLoading = ref(false)
        const selectedKeluhan = ref(null)
        const showViewModal = ref(false)
        const selectedViewKeluhan = ref(null)
        const formErrors = ref({})

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

        const validateForm = () => {
            const errors = {}
            // Nama: max 50 karakter
            if (!formData.value.nama) {
                errors.nama = 'Nama wajib diisi.'
            } else if (formData.value.nama.length > 50) {
                errors.nama = 'Text too long, maximum 50 characters.'
            }
            // Email: validasi sederhana
            if (!formData.value.email) {
                errors.email = 'Email wajib diisi.'
            } else if (!/^\S+@\S+\.\S+$/.test(formData.value.email)) {
                errors.email = 'Format email tidak valid.'
            }
            // Nomor HP: hanya angka
            if (!formData.value.nomor_hp) {
                errors.nomor_hp = 'Nomor HP wajib diisi.'
            } else if (!/^\d+$/.test(formData.value.nomor_hp)) {
                errors.nomor_hp = 'Input numeric only.'
            }
            // Status: harus dipilih
            if (formData.value.status_keluhan === '' || formData.value.status_keluhan === null || formData.value.status_keluhan === undefined) {
                errors.status_keluhan = 'Status wajib dipilih.'
            }
            // Keluhan: tidak boleh kosong
            if (!formData.value.keluhan) {
                errors.keluhan = 'Keluhan wajib diisi.'
            }
            formErrors.value = errors
            return Object.keys(errors).length === 0
        }

        // Save keluhan (create or update)
        const saveKeluhan = async () => {
            if (!validateForm()) return
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
            selectedViewKeluhan.value = keluhan
            showViewModal.value = true
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
            formErrors.value = {}
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

        const showHistory = async (keluhan) => {
            selectedKeluhan.value = keluhan
            showHistoryModal.value = true
            historyLoading.value = true
            try {
                const response = await axios.get(`/api/keluhan-status-history?keluhan_id=${keluhan.id}`)
                // Filter data jika API mengembalikan semua history
                keluhanHistory.value = Array.isArray(response.data)
                    ? response.data.filter(item => item.keluhan_id === keluhan.id)
                        .sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at))
                    : []
            } catch (error) {
                keluhanHistory.value = []
            } finally {
                historyLoading.value = false
            }
        }
        const closeHistoryModal = () => {
            showHistoryModal.value = false
            keluhanHistory.value = []
            selectedKeluhan.value = null
        }

        const closeViewModal = () => {
            showViewModal.value = false
            selectedViewKeluhan.value = null
        }

        const exportData = (format) => {
            window.open(`/api/keluhan-pelanggan/export/${format}`, '_blank')
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
            formatDate,
            showHistoryModal,
            keluhanHistory,
            historyLoading,
            selectedKeluhan,
            showHistory,
            closeHistoryModal,
            showViewModal,
            selectedViewKeluhan,
            closeViewModal,
            formErrors,
            validateForm,
            exportData
        }
    }
}
</script>

<style scoped>
.modal {
    z-index: 1050;
}
.timeline {
    border-left: 2px solid #dee2e6;
    margin-left: 1rem;
    padding-left: 1rem;
}
.timeline li {
    position: relative;
}
.timeline li:before {
    content: '';
    position: absolute;
    left: -1.1rem;
    top: 0.5rem;
    width: 0.8rem;
    height: 0.8rem;
    background: #fff;
    border: 2px solid #6c757d;
    border-radius: 50%;
}
</style>