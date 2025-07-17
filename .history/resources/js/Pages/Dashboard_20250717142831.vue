<template>
  <div class="container py-4">
    <h2 class="mb-4">Dashboard Keluhan Pelanggan</h2>
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Persentase Keluhan by Status</div>
          <div class="card-body">
            <canvas id="pieChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Jumlah Keluhan per Status (6 Bulan Terakhir)</div>
          <div class="card-body">
            <canvas id="barChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">Top 10 Keluhan dengan Umur Terlama</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Nomor HP</th>
                <th>Status</th>
                <th>Keluhan</th>
                <th>Tanggal Dibuat</th>
                <th>Umur (hari)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in topAging" :key="item.id">
                <td>{{ idx + 1 }}</td>
                <td>{{ item.nama }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.nomor_hp }}</td>
                <td>{{ getStatusText(item.status_keluhan) }}</td>
                <td>{{ item.keluhan }}</td>
                <td>{{ formatDate(item.created_at) }}</td>
                <td>{{ item.umur }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
let pieChart, barChart
const topAging = ref([])

const getStatusText = (status) => {
  const texts = { '0': 'Received', '1': 'In Process', '2': 'Done' }
  return texts[status] || status
}
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

onMounted(async () => {
  // ! Pie Chart
  const pieRes = await axios.get('/api/dashboard/summary-status')
  const pieData = pieRes.data
  const pieLabels = pieData.map(d => getStatusText(d.status_keluhan))
  const pieValues = pieData.map(d => d.total)
  const pieColors = ['#ffc107', '#17a2b8', '#28a745']
  if (window.Chart) {
    pieChart = new window.Chart(document.getElementById('pieChart'), {
      type: 'pie',
      data: { labels: pieLabels, datasets: [{ data: pieValues, backgroundColor: pieColors }] },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    })
  }

  // ! Bar Chart
  const barRes = await axios.get('/api/dashboard/status-per-month')
  const barData = barRes.data
  
  const months = [...new Set(barData.map(d => d.bulan))]
  const statusList = ['0', '1', '2']
  const barDatasets = statusList.map((status, i) => ({
    label: getStatusText(status),
    backgroundColor: pieColors[i],
    data: months.map(m => {
      const found = barData.find(d => d.bulan === m && d.status_keluhan === status)
      return found ? found.total : 0
    })
  }))
  if (window.Chart) {
    barChart = new window.Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: { labels: months, datasets: barDatasets },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
    })
  }

  //! Top Aging Table
  const agingRes = await axios.get('/api/dashboard/top-aging')
  topAging.value = agingRes.data
})
</script>



<style scoped>
.card { margin-bottom: 1.5rem; }
</style> 