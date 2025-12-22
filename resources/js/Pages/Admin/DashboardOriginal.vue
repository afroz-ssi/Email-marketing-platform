<template>
  <div>
    <Head title="Dashboard" />
    
    <!-- Key Metrics Cards -->
    <div class="row mb-4">
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Total Emails Sent</h4>
              <span class="kt-widget24__desc">All time email sends</span>
            </div>
            <span class="kt-widget24__stats kt-font-success">{{ userData?.totalSent || 0 }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Total Leads</h4>
              <span class="kt-widget24__desc">Contacts in database</span>
            </div>
            <span class="kt-widget24__stats kt-font-brand">
              <Link :href="route('admin.leads')">{{ userData?.totalLeads || 0 }}</Link>
            </span>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Active Campaigns</h4>
              <span class="kt-widget24__desc">Running campaigns</span>
            </div>
            <span class="kt-widget24__stats kt-font-warning">
              <Link :href="route('admin.campaigns')">{{ userData?.totalCampaigns || 0 }}</Link>
            </span>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Outlook Accounts</h4>
              <span class="kt-widget24__desc">{{ userData?.activeOutlookAccounts || 0 }} active / {{ userData?.totalOutlookAccounts || 0 }} total</span>
            </div>
            <span class="kt-widget24__stats kt-font-info">
              <Link :href="route('admin.outlookAccounts')">{{ userData?.totalOutlookAccounts || 0 }}</Link>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
      <!-- Email Trends Chart -->
      <div class="col-xl-8">
        <div class="kt-portlet kt-portlet--height-fluid">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Email Sending Trends</h3>
            </div>
            <div class="kt-portlet__head-toolbar">
              <div class="btn-group">
                <button 
                  @click="chartPeriod = 'weekly'" 
                  :class="['btn btn-sm', chartPeriod === 'weekly' ? 'btn-primary' : 'btn-secondary']"
                >
                  Weekly
                </button>
                <button 
                  @click="chartPeriod = 'monthly'" 
                  :class="['btn btn-sm', chartPeriod === 'monthly' ? 'btn-primary' : 'btn-secondary']"
                >
                  Monthly
                </button>
              </div>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="area"
              height="300"
              :options="emailTrendsOptions"
              :series="emailTrendsSeries"
            />
          </div>
        </div>
      </div>
      
      <!-- Campaign Performance -->
      <div class="col-xl-4">
        <div class="kt-portlet kt-portlet--height-fluid">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Top Campaigns</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="donut"
              height="300"
              :options="campaignDonutOptions"
              :series="campaignDonutSeries"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Outlook Accounts Usage -->
    <div class="row mt-4">
      <div class="col-xl-6">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Outlook Account Usage</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="bar"
              height="300"
              :options="outlookUsageOptions"
              :series="outlookUsageSeries"
            />
          </div>
        </div>
      </div>
      
      <!-- Lead Sources -->
      <div class="col-xl-6">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Lead Sources</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="pie"
              height="300"
              :options="leadSourcesOptions"
              :series="leadSourcesSeries"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Campaign Stats Table -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Recent Campaign Performance</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Campaign Name</th>
                    <th>Emails Sent</th>
                    <th>Created Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="campaign in userData?.campaignStats" :key="campaign.name">
                    <td>{{ campaign.name }}</td>
                    <td>{{ campaign.sent_count }}</td>
                    <td>{{ formatDate(campaign.created_at) }}</td>
                    <td>
                      <span :class="['kt-badge kt-badge--inline', campaign.sent_count > 0 ? 'kt-badge--success' : 'kt-badge--warning']">
                        {{ campaign.sent_count > 0 ? 'Active' : 'Pending' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

const props = defineProps(["data"]);
const userData = ref({});
const chartPeriod = ref('monthly');

userData.value = props.data;

// Email Trends Chart
const emailTrendsSeries = computed(() => {
  const data = chartPeriod.value === 'weekly' ? userData.value?.weeklyEmails : userData.value?.monthlyEmails;
  return [{
    name: 'Emails Sent',
    data: data?.map(item => item.total) || []
  }];
});

const emailTrendsOptions = computed(() => ({
  chart: {
    type: 'area',
    toolbar: { show: false },
    zoom: { enabled: false }
  },
  colors: ['#5d78ff'],
  dataLabels: { enabled: false },
  stroke: {
    curve: 'smooth',
    width: 3
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.3
    }
  },
  xaxis: {
    categories: (chartPeriod.value === 'weekly' ? userData.value?.weeklyEmails : userData.value?.monthlyEmails)?.map(item => item.day || item.month) || []
  },
  yaxis: {
    title: { text: 'Number of Emails' }
  },
  tooltip: {
    y: {
      formatter: (val) => `${val} emails`
    }
  }
}));

// Campaign Donut Chart
const campaignDonutSeries = computed(() => 
  userData.value?.campaigns?.slice(0, 5).map(c => c.sent_count) || []
);

const campaignDonutOptions = computed(() => ({
  chart: {
    type: 'donut'
  },
  labels: userData.value?.campaigns?.slice(0, 5).map(c => c.name) || [],
  colors: ['#5d78ff', '#00c5dc', '#ffb822', '#fd397a', '#5578eb'],
  legend: {
    position: 'bottom'
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%'
      }
    }
  },
  tooltip: {
    y: {
      formatter: (val) => `${val} emails`
    }
  }
}));

// Outlook Usage Chart
const outlookUsageSeries = computed(() => [
  {
    name: 'Sent Today',
    data: userData.value?.outlookUsage?.map(acc => acc.sent_today) || []
  },
  {
    name: 'Daily Limit',
    data: userData.value?.outlookUsage?.map(acc => acc.daily_limit) || []
  }
]);

const outlookUsageOptions = computed(() => ({
  chart: {
    type: 'bar',
    toolbar: { show: false }
  },
  colors: ['#fd397a', '#00c5dc'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%'
    }
  },
  dataLabels: { enabled: false },
  xaxis: {
    categories: userData.value?.outlookUsage?.map(acc => acc.email.split('@')[0]) || []
  },
  yaxis: {
    title: { text: 'Number of Emails' }
  },
  legend: {
    position: 'top'
  }
}));

// Lead Sources Chart
const leadSourcesSeries = computed(() => 
  userData.value?.leadSources?.map(source => source.count) || []
);

const leadSourcesOptions = computed(() => ({
  chart: {
    type: 'pie'
  },
  labels: userData.value?.leadSources?.map(source => source.source) || [],
  colors: ['#5d78ff', '#00c5dc', '#ffb822', '#fd397a', '#5578eb', '#36a3f7', '#34bfa3'],
  legend: {
    position: 'bottom'
  }
}));

// Utility functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

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
