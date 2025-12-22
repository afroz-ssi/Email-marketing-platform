<template>
  <div class="kt-portlet">
    <div class="kt-portlet__head">
      <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">Web Scraper</h3>
      </div>
    </div>
    <div class="kt-portlet__body">
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
          <div class="kt-checkbox-inline">
            <label class="kt-checkbox">
              <input type="checkbox" v-model="form.autoCreateLeads" />
              Auto-create leads from scraped data
              <span></span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <button
            type="submit"
            class="btn btn-primary"
            :disabled="loading"
          >
            <i class="la la-search" v-if="!loading"></i>
            <i class="la la-spinner la-spin" v-else></i>
            {{ loading ? 'Scraping...' : 'Scrape Website' }}
          </button>
        </div>
      </form>

      <!-- Results -->
      <div v-if="results" class="mt-4">
        <div class="alert alert-success" v-if="results.success">
          <h5>Scraping Results for {{ results.website }}</h5>
          <p><strong>Company:</strong> {{ results.company }}</p>
          <p><strong>Total Found:</strong> {{ results.total_found }} contacts</p>
          
          <div class="row mt-3">
            <div class="col-md-6" v-if="results.emails.length > 0">
              <h6>Emails Found ({{ results.emails.length }})</h6>
              <ul class="list-group">
                <li 
                  v-for="email in results.emails" 
                  :key="email"
                  class="list-group-item d-flex justify-content-between align-items-center"
                >
                  {{ email }}
                  <button 
                    @click="createLead(email, 'email')"
                    class="btn btn-sm btn-outline-primary"
                    v-if="!results.leads_created"
                  >
                    Add Lead
                  </button>
                </li>
              </ul>
            </div>
            
            <div class="col-md-6" v-if="results.phones.length > 0">
              <h6>Phone Numbers Found ({{ results.phones.length }})</h6>
              <ul class="list-group">
                <li 
                  v-for="phone in results.phones" 
                  :key="phone"
                  class="list-group-item"
                >
                  {{ phone }}
                </li>
              </ul>
            </div>
          </div>
          
          <div v-if="results.leads_created" class="alert alert-info mt-3">
            <i class="la la-check"></i> Leads have been automatically created from the scraped data.
          </div>
        </div>
        
        <div class="alert alert-danger" v-else>
          <h5>Scraping Failed</h5>
          <p>{{ results.message }}</p>
        </div>
      </div>

      <!-- Bulk Scraper -->
      <div class="mt-5">
        <h5>Bulk Website Scraper</h5>
        <div class="form-group">
          <label>Website URLs (one per line, max 10)</label>
          <textarea
            v-model="bulkWebsites"
            class="form-control"
            rows="5"
            placeholder="https://example1.com&#10;https://example2.com&#10;https://example3.com"
          ></textarea>
        </div>
        
        <button
          @click="bulkScrape"
          class="btn btn-info"
          :disabled="bulkLoading || !bulkWebsites.trim()"
        >
          <i class="la la-globe" v-if="!bulkLoading"></i>
          <i class="la la-spinner la-spin" v-else></i>
          {{ bulkLoading ? 'Bulk Scraping...' : 'Bulk Scrape Websites' }}
        </button>
      </div>

      <!-- Bulk Results -->
      <div v-if="bulkResults" class="mt-4">
        <h6>Bulk Scraping Results</h6>
        <div class="accordion" id="bulkResultsAccordion">
          <div 
            v-for="(result, index) in bulkResults.results" 
            :key="index"
            class="card"
          >
            <div class="card-header">
              <button
                class="btn btn-link"
                type="button"
                :data-target="`#collapse${index}`"
                data-toggle="collapse"
              >
                {{ result.website }} 
                <span :class="['badge ml-2', result.success ? 'badge-success' : 'badge-danger']">
                  {{ result.success ? `${result.total_found} found` : 'Failed' }}
                </span>
              </button>
            </div>
            <div 
              :id="`collapse${index}`" 
              class="collapse" 
              data-parent="#bulkResultsAccordion"
            >
              <div class="card-body">
                <div v-if="result.success">
                  <p><strong>Company:</strong> {{ result.company }}</p>
                  <p><strong>Emails:</strong> {{ result.emails.join(', ') || 'None' }}</p>
                  <p><strong>Phones:</strong> {{ result.phones.join(', ') || 'None' }}</p>
                </div>
                <div v-else>
                  <p class="text-danger">{{ result.message }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

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
    results.value = {
      success: false,
      message: 'Network error occurred while scraping'
    };
  } finally {
    loading.value = false;
  }
};

const bulkScrape = async () => {
  const websites = bulkWebsites.value
    .split('\n')
    .map(url => url.trim())
    .filter(url => url.length > 0);
    
  if (websites.length === 0 || websites.length > 10) {
    alert('Please provide 1-10 valid website URLs');
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
    bulkResults.value = {
      success: false,
      message: 'Network error occurred during bulk scraping'
    };
  } finally {
    bulkLoading.value = false;
  }
};

const createLead = (contact, type) => {
  const leadData = {
    company: results.value.company,
    website: results.value.website,
    [type]: contact
  };
  
  router.post(route('admin.createLead'), leadData, {
    onSuccess: () => {
      alert('Lead created successfully!');
    },
    onError: () => {
      alert('Failed to create lead');
    }
  });
};
</script>

<style scoped>
.list-group-item {
  font-size: 0.9rem;
}

.badge {
  font-size: 0.75rem;
}

.card-header button {
  text-decoration: none;
  color: #495057;
  width: 100%;
  text-align: left;
}

.card-header button:hover {
  text-decoration: none;
}
</style>