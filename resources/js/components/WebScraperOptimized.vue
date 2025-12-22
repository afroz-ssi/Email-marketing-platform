<template>
  <div class="kt-portlet">
    <div class="kt-portlet__head">
      <h3 class="kt-portlet__head-title">Web Scraper</h3>
    </div>
    <div class="kt-portlet__body">
      <!-- Single Website Scraper -->
      <form @submit.prevent="scrapeWebsite">
        <div class="form-group">
          <label>Website URL</label>
          <input
            type="url"
            v-model="form.website"
            class="form-control"
            placeholder="https://example.com"
            required
          />
        </div>
        
        <div class="form-group">
          <label class="kt-checkbox">
            <input type="checkbox" v-model="form.autoCreateLeads" />
            Auto-create leads from scraped data
            <span></span>
          </label>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="loading">
          <i :class="loading ? 'la la-spinner la-spin' : 'la la-search'"></i>
          {{ loading ? 'Scraping...' : 'Scrape Website' }}
        </button>
      </form>

      <!-- Results -->
      <div v-if="results" class="mt-4">
        <div v-if="results.success" class="alert alert-success">
          <h5>Found {{ results.total_found }} contacts from {{ results.company }}</h5>
          
          <div class="row mt-3" v-if="results.emails.length || results.phones.length">
            <div class="col-md-6" v-if="results.emails.length">
              <h6>Emails ({{ results.emails.length }})</h6>
              <div class="list-group">
                <div v-for="email in results.emails" :key="email" class="list-group-item">
                  {{ email }}
                </div>
              </div>
            </div>
            
            <div class="col-md-6" v-if="results.phones.length">
              <h6>Phones ({{ results.phones.length }})</h6>
              <div class="list-group">
                <div v-for="phone in results.phones" :key="phone" class="list-group-item">
                  {{ phone }}
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="results.leads_created" class="alert alert-info mt-3">
            ✓ Leads created automatically
          </div>
        </div>
        
        <div v-else class="alert alert-danger">
          <strong>Failed:</strong> {{ results.message }}
        </div>
      </div>

      <!-- Bulk Scraper -->
      <hr class="mt-5">
      <h5>Bulk Scraper</h5>
      <div class="form-group">
        <label>Website URLs (one per line, max 10)</label>
        <textarea
          v-model="bulkWebsites"
          class="form-control"
          rows="4"
          placeholder="https://example1.com&#10;https://example2.com"
        ></textarea>
      </div>
      
      <button @click="bulkScrape" class="btn btn-info" :disabled="bulkLoading || !bulkWebsites.trim()">
        <i :class="bulkLoading ? 'la la-spinner la-spin' : 'la la-globe'"></i>
        {{ bulkLoading ? 'Bulk Scraping...' : 'Bulk Scrape' }}
      </button>

      <!-- Bulk Results -->
      <div v-if="bulkResults" class="mt-4">
        <h6>Bulk Results</h6>
        <div v-for="(result, index) in bulkResults.results" :key="index" class="card mb-2">
          <div class="card-header">
            {{ result.website }}
            <span :class="['badge ml-2', result.success ? 'badge-success' : 'badge-danger']">
              {{ result.success ? `${result.total_found} found` : 'Failed' }}
            </span>
          </div>
          <div v-if="result.success" class="card-body">
            <small>
              <strong>Company:</strong> {{ result.company }}<br>
              <strong>Emails:</strong> {{ result.emails.join(', ') || 'None' }}<br>
              <strong>Phones:</strong> {{ result.phones.join(', ') || 'None' }}
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const loading = ref(false);
const bulkLoading = ref(false);
const results = ref(null);
const bulkResults = ref(null);
const bulkWebsites = ref('');

const form = reactive({
  website: '',
  autoCreateLeads: true
});

const scrapeWebsite = async () => {
  loading.value = true;
  results.value = null;
  
  try {
    const response = await fetch('/admin/scrape', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        website: form.website,
        auto_create_leads: form.autoCreateLeads
      })
    });
    
    results.value = await response.json();
  } catch (error) {
    results.value = { success: false, message: 'Network error occurred' };
  } finally {
    loading.value = false;
  }
};

const bulkScrape = async () => {
  const websites = bulkWebsites.value.split('\n').map(url => url.trim()).filter(url => url);
    
  if (websites.length === 0 || websites.length > 10) {
    alert('Please provide 1-10 valid URLs');
    return;
  }
  
  bulkLoading.value = true;
  bulkResults.value = null;
  
  try {
    const response = await fetch('/admin/bulk-scrape', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ websites })
    });
    
    bulkResults.value = await response.json();
  } catch (error) {
    bulkResults.value = { success: false, message: 'Network error occurred' };
  } finally {
    bulkLoading.value = false;
  }
};
</script>

<style scoped>
.list-group-item {
  font-size: 0.9rem;
  padding: 8px 12px;
}

.card-header {
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>