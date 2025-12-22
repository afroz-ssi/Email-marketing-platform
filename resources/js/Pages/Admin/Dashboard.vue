<template>
  <div>
    <Head title="Dashboard" />
    
    <!-- Key Metrics -->
    <div class="row mb-4">
      <div class="col-xl-3 col-lg-6" v-for="metric in metrics" :key="metric.title">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">{{ metric.title }}</h4>
              <span class="kt-widget24__desc">{{ metric.desc }}</span>
            </div>
            <span :class="['kt-widget24__stats', metric.color]">{{ metric.value }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row">
      <div class="col-xl-8">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <h3 class="kt-portlet__head-title mt-3">Email Trends</h3>
            <div class="btn-group">
              <button @click="chartPeriod = 'weekly'" :class="['btn btn-sm', chartPeriod === 'weekly' ? 'btn-primary' : 'btn-secondary']">Weekly</button>
              <button @click="chartPeriod = 'monthly'" :class="['btn btn-sm', chartPeriod === 'monthly' ? 'btn-primary' : 'btn-secondary']">Monthly</button>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart type="area" height="300" :options="emailTrendsOptions" :series="emailTrendsSeries" />
          </div>
        </div>
      </div>
      
      <div class="col-xl-4">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <h3 class="kt-portlet__head-title mt-3">Top Campaigns</h3>
          </div>
          <div class="kt-portlet__body">
            <apexchart type="donut" height="300" :options="campaignOptions" :series="campaignSeries" />
          </div>
        </div>
      </div>
    </div>

    <!-- Outlook Usage -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <h3 class="kt-portlet__head-title mt-3">Outlook Account Usage</h3>
          </div>
          <div class="kt-portlet__body">
            <apexchart type="bar" height="250" :options="outlookOptions" :series="outlookSeries" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

const props = defineProps(["data"]);
const chartPeriod = ref('weekly');

// Metrics
const metrics = computed(() => [
  { title: 'Total Emails Sent', desc: 'All time sends', value: props.data?.totalSent || 0, color: 'kt-font-success' },
  { title: 'Total Leads', desc: 'Contacts in database', value: props.data?.totalLeads || 0, color: 'kt-font-brand' },
  { title: 'Campaigns', desc: 'Total campaigns', value: props.data?.totalCampaigns || 0, color: 'kt-font-warning' },
  { title: 'Outlook Accounts', desc: `${props.data?.activeOutlookAccounts || 0} active`, value: props.data?.totalOutlookAccounts || 0, color: 'kt-font-info' }
]);

// Email Trends
const emailTrendsSeries = computed(() => [{
  name: 'Emails Sent',
  data: (chartPeriod.value === 'weekly' ? props.data?.weeklyEmails : props.data?.monthlyEmails)?.map(item => item.total) || []
}]);

const emailTrendsOptions = computed(() => ({
  chart: { type: 'area', toolbar: { show: false } },
  colors: ['#5d78ff'],
  stroke: { curve: 'smooth', width: 3 },
  fill: { type: 'gradient', gradient: { opacityFrom: 0.7, opacityTo: 0.3 } },
  xaxis: { categories: (chartPeriod.value === 'weekly' ? props.data?.weeklyEmails : props.data?.monthlyEmails)?.map(item => item.day || item.month) || [] }
}));

// Campaigns
const campaignSeries = computed(() => props.data?.campaigns?.map(c => c.sent_count) || []);
const campaignOptions = computed(() => ({
  labels: props.data?.campaigns?.map(c => c.name) || [],
  colors: ['#5d78ff', '#00c5dc', '#ffb822', '#fd397a', '#5578eb']
}));

// Outlook Usage
const outlookSeries = computed(() => [{
  name: 'Sent Today',
  data: props.data?.outlookUsage?.map(acc => acc.sent_today) || []
}, {
  name: 'Daily Limit',
  data: props.data?.outlookUsage?.map(acc => acc.daily_limit) || []
}]);

const outlookOptions = computed(() => ({
  chart: { type: 'bar' },
  colors: ['#fd397a', '#00c5dc'],
  xaxis: { categories: props.data?.outlookUsage?.map(acc => acc.email.split('@')[0]) || [] }
}));

onMounted(() => {
  emit.emit("pageName", "Dashboard", []);
});
</script>

<style scoped>
.kt-widget24 {
  background: #fff;
  border-radius: 4px;
  padding: 20px;
  box-shadow: 0 0 13px 0 rgba(82, 63, 105, 0.05);
  margin-bottom: 20px;
}

.kt-widget24__details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.kt-widget24__title {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
}

.kt-widget24__desc {
  font-size: 0.9rem;
  color: #74788d;
}

.kt-widget24__stats {
  font-size: 2rem;
  font-weight: 700;
}

.kt-font-success { color: #1bc5bd; }
.kt-font-brand { color: #5d78ff; }
.kt-font-warning { color: #ffb822; }
.kt-font-info { color: #00c5dc; }
</style>
