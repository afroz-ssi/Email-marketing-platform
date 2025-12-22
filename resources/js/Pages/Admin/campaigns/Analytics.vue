<template>
  <div>
    <Head title="Campaign Analytics" />
    
    <!-- Key Metrics Cards -->
    <div class="row mb-4">
      <div class="col-xl-3 col-lg-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Total Campaigns</h4>
              <span class="kt-widget24__desc">All campaigns created</span>
            </div>
            <span class="kt-widget24__stats kt-font-brand">{{ data?.total_campaigns || 0 }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-lg-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Active Campaigns</h4>
              <span class="kt-widget24__desc">Currently running</span>
            </div>
            <span class="kt-widget24__stats kt-font-success">{{ data?.active_campaigns || 0 }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-lg-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Total Emails Sent</h4>
              <span class="kt-widget24__desc">All time sends</span>
            </div>
            <span class="kt-widget24__stats kt-font-info">{{ data?.total_emails_sent || 0 }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-lg-6">
        <div class="kt-widget24">
          <div class="kt-widget24__details">
            <div class="kt-widget24__info">
              <h4 class="kt-widget24__title">Emails Today</h4>
              <span class="kt-widget24__desc">Sent today</span>
            </div>
            <span class="kt-widget24__stats kt-font-warning">{{ data?.emails_sent_today || 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
      <!-- Daily Email Trends -->
      <div class="col-xl-8">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Daily Email Sending Trends (Last 30 Days)</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="line"
              height="350"
              :options="dailyTrendsOptions"
              :series="dailyTrendsSeries"
            />
          </div>
        </div>
      </div>
      
      <!-- Top Campaigns -->
      <div class="col-xl-4">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Top Performing Campaigns</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="donut"
              height="350"
              :options="topCampaignsOptions"
              :series="topCampaignsSeries"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Outlook Performance -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Outlook Account Performance</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <apexchart
              type="bar"
              height="300"
              :options="outlookPerformanceOptions"
              :series="outlookPerformanceSeries"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity & Top Campaigns Tables -->
    <div class="row mt-4">
      <!-- Recent Activity -->
      <div class="col-xl-8">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Recent Email Activity</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Campaign</th>
                    <th>Recipient</th>
                    <th>Outlook Account</th>
                    <th>Sent At</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="activity in data?.recent_activity?.slice(0, 10)" :key="activity.id">
                    <td>{{ activity.campaign?.name || 'N/A' }}</td>
                    <td>{{ activity.lead?.email || 'N/A' }}</td>
                    <td>{{ activity.outlook_email }}</td>
                    <td>{{ formatDateTime(activity.sent_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Top Campaigns List -->
      <div class="col-xl-4">
        <div class="kt-portlet">
          <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
              <h3 class="kt-portlet__head-title">Campaign Leaderboard</h3>
            </div>
          </div>
          <div class="kt-portlet__body">
            <div class="kt-widget4">
              <div 
                v-for="(campaign, index) in data?.top_campaigns?.slice(0, 8)" 
                :key="campaign.id"
                class="kt-widget4__item"
              >
                <div class="kt-widget4__pic kt-widget4__pic--pic">
                  <span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">
                    {{ index + 1 }}
                  </span>
                </div>
                <div class="kt-widget4__info">
                  <span class="kt-widget4__username">{{ campaign.name }}</span>
                  <span class="kt-widget4__text">{{ campaign.sent_count }} emails sent</span>
                </div>
                <span class="kt-widget4__number kt-font-info">
                  {{ campaign.sent_count }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';

const props = defineProps(['data']);

// Daily Trends Chart
const dailyTrendsSeries = computed(() => [{
  name: 'Emails Sent',
  data: props.data?.daily_stats?.map(stat => stat.count) || []
}]);

const dailyTrendsOptions = computed(() => ({
  chart: {
    type: 'line',
    toolbar: { show: false },
    zoom: { enabled: false }
  },
  colors: ['#5d78ff'],
  dataLabels: { enabled: false },
  stroke: {
    curve: 'smooth',
    width: 3
  },
  markers: {
    size: 4,
    colors: ['#ffffff'],
    strokeColors: '#5d78ff',
    strokeWidth: 2
  },
  xaxis: {
    categories: props.data?.daily_stats?.map(stat => {
      return new Date(stat.date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
      });
    }) || []
  },
  yaxis: {
    title: { text: 'Number of Emails' }
  },
  tooltip: {
    y: {
      formatter: (val) => `${val} emails`
    }
  },
  grid: {
    borderColor: '#e7eaed'
  }
}));

// Top Campaigns Donut Chart
const topCampaignsSeries = computed(() => 
  props.data?.top_campaigns?.slice(0, 6).map(c => c.sent_count) || []
);

const topCampaignsOptions = computed(() => ({
  chart: {
    type: 'donut'
  },
  labels: props.data?.top_campaigns?.slice(0, 6).map(c => c.name) || [],
  colors: ['#5d78ff', '#00c5dc', '#ffb822', '#fd397a', '#5578eb', '#36a3f7'],
  legend: {
    position: 'bottom'
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%'
      }
    }
  },
  tooltip: {
    y: {
      formatter: (val) => `${val} emails`
    }
  }
}));

// Outlook Performance Chart
const outlookPerformanceSeries = computed(() => [
  {
    name: 'Sent Today',
    data: props.data?.outlook_performance?.map(acc => acc.sent_today) || []
  },
  {
    name: 'Daily Limit',
    data: props.data?.outlook_performance?.map(acc => acc.daily_limit) || []
  },
  {
    name: 'Usage %',
    data: props.data?.outlook_performance?.map(acc => acc.usage_percentage) || []
  }
]);

const outlookPerformanceOptions = computed(() => ({
  chart: {
    type: 'bar',
    toolbar: { show: false }
  },
  colors: ['#fd397a', '#00c5dc', '#ffb822'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      endingShape: 'rounded'
    }
  },
  dataLabels: { enabled: false },
  xaxis: {
    categories: props.data?.outlook_performance?.map(acc => 
      acc.email.split('@')[0]
    ) || []
  },
  yaxis: {
    title: { text: 'Count / Percentage' }
  },
  legend: {
    position: 'top'
  },
  tooltip: {
    y: {
      formatter: (val, { seriesIndex }) => {
        if (seriesIndex === 2) return `${val}%`;
        return `${val} emails`;
      }
    }
  }
}));

// Utility functions
const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleString();
};

onMounted(() => {
  emit.emit("pageName", "Campaign Analytics", [
    {
      title: "Campaigns",
      routeName: "admin.campaigns",
    },
    {
      title: "Analytics",
      routeName: "admin.campaignAnalytics",
    },
  ]);
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

.kt-widget4__item {
  display: flex;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid #ebedf2;
}

.kt-widget4__pic {
  margin-right: 15px;
}

.kt-widget4__info {
  flex: 1;
}

.kt-widget4__username {
  display: block;
  font-weight: 600;
  font-size: 0.9rem;
}

.kt-widget4__text {
  display: block;
  font-size: 0.8rem;
  color: #74788d;
}

.kt-widget4__number {
  font-weight: 600;
  font-size: 1.1rem;
}
</style>