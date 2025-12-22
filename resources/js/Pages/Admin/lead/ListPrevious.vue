<template>
  <div>
    <Head title="Lead Lists" />
    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__body">
        <div class="dataTables_wrapper dt-bootstrap4 no-footer">
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <perPageDropdown />
            </div>
            <div class="col-sm-12 col-md-6">
              <div class="dataTables_filter">
                <button @click="showScraper = !showScraper" class="btn btn-info mr-2">
                  <i class="la la-globe"></i>Web Scraper
                </button>
                <Link :href="route('admin.createLead')" class="btn btn-primary">
                  <i class="la la-plus"></i>Add New
                </Link>
              </div>
            </div>
          </div>
          
          <!-- Web Scraper Component -->
          <div v-if="showScraper" class="row mb-4">
            <div class="col-12">
              <div class="kt-portlet">
                <div class="kt-portlet__head">
                  <h3 class="kt-portlet__head-title">Web Scraper</h3>
                </div>
                <div class="kt-portlet__body">
                  <!-- Single Website Scraper -->
                  <form @submit.prevent="scrapeWebsite">
                    <div class="form-group">
                      <label>Website URL</label>
                      <input type="url" v-model="scraperForm.website" class="form-control" placeholder="https://example.com" required />
                    </div>
                    
                    <div class="form-group">
                      <label class="kt-checkbox">
                        <input type="checkbox" v-model="scraperForm.autoCreateLeads" />
                        Auto-create leads from scraped data
                        <span></span>
                      </label>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="scrapeLoading">
                      <i :class="scrapeLoading ? 'la la-spinner la-spin' : 'la la-search'"></i>
                      {{ scrapeLoading ? 'Scraping...' : 'Scrape Website' }}
                    </button>
                  </form>

                  <!-- Results -->
                  <div v-if="scrapeResults" class="mt-4">
                    <div v-if="scrapeResults.success" class="alert alert-success">
                      <h5>Found {{ scrapeResults.total_found }} contacts from {{ scrapeResults.company }}</h5>
                      
                      <div class="row mt-3" v-if="scrapeResults.emails.length || scrapeResults.phones.length">
                        <div class="col-md-6" v-if="scrapeResults.emails.length">
                          <h6>Emails ({{ scrapeResults.emails.length }})</h6>
                          <div class="list-group">
                            <div v-for="email in scrapeResults.emails" :key="email" class="list-group-item">
                              {{ email }}
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-6" v-if="scrapeResults.phones.length">
                          <h6>Phones ({{ scrapeResults.phones.length }})</h6>
                          <div class="list-group">
                            <div v-for="phone in scrapeResults.phones" :key="phone" class="list-group-item">
                              {{ phone }}
                            </div>
                          </div>
                        </div>
                      </div>
                      
                      <div v-if="scrapeResults.leads_created" class="alert alert-success mt-3">
                        <h5><i class="la la-check-circle"></i> Success! Leads Created</h5>
                        <p class="mb-2"><strong>{{ scrapeResults.emails.length }}</strong> email(s) and <strong>{{ scrapeResults.phones.length }}</strong> phone number(s) have been saved to your leads database.</p>
                        <div class="row">
                          <div class="col-md-6" v-if="scrapeResults.emails.length">
                            <h6><i class="la la-envelope"></i> Emails Added:</h6>
                            <ul class="list-unstyled">
                              <li v-for="email in scrapeResults.emails" :key="email" class="text-success">
                                <i class="la la-check"></i> {{ email }}
                              </li>
                            </ul>
                          </div>
                          <div class="col-md-6" v-if="scrapeResults.phones.length">
                            <h6><i class="la la-phone"></i> Phones Added:</h6>
                            <ul class="list-unstyled">
                              <li v-for="phone in scrapeResults.phones" :key="phone" class="text-success">
                                <i class="la la-check"></i> {{ phone }}
                              </li>
                            </ul>
                          </div>
                        </div>
                        <p class="mb-0"><i class="la la-info-circle"></i> <strong>Check the leads table below</strong> to see your new contacts!</p>
                      </div>
                    </div>
                    
                    <div v-else class="alert alert-danger">
                      <strong>Failed:</strong> {{ scrapeResults.message }}
                    </div>
                  </div>

                  <!-- Bulk Scraper -->
                  <hr class="mt-4">
                  <h5>Bulk Scraper</h5>
                  <div class="form-group">
                    <label>Website URLs (one per line, max 10)</label>
                    <textarea v-model="bulkWebsites" class="form-control" rows="4" placeholder="https://example1.com&#10;https://example2.com"></textarea>
                  </div>
                  
                  <button @click="bulkScrape" class="btn btn-info" :disabled="bulkLoading || !bulkWebsites.trim()">
                    <i :class="bulkLoading ? 'la la-spinner la-spin' : 'la la-globe'"></i>
                    {{ bulkLoading ? 'Bulk Scraping...' : 'Bulk Scrape' }}
                  </button>

                  <!-- Bulk Results -->
                  <div v-if="bulkResults" class="mt-4">
                    <div class="alert alert-success">
                      <h5><i class="la la-check-circle"></i> Bulk Scraping Complete!</h5>
                      <p>Processed {{ bulkResults.total_websites }} websites and found contacts from multiple sources.</p>
                    </div>
                    <div v-for="(result, index) in bulkResults.results" :key="index" class="card mb-2">
                      <div class="card-header">
                        {{ result.website }}
                        <span :class="['badge ml-2', result.success ? 'badge-success' : 'badge-danger']">
                          {{ result.success ? `${result.total_found} found` : 'Failed' }}
                        </span>
                      </div>
                      <div v-if="result.success" class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <strong>Company:</strong> {{ result.company }}<br>
                            <strong>Emails:</strong> {{ result.emails.join(', ') || 'None' }}
                          </div>
                          <div class="col-md-6">
                            <strong>Phones:</strong> {{ result.phones.join(', ') || 'None' }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row table-responsive table_fixed_width">
            <div class="col-sm-12">
              <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer">
                <thead>
                  <tr>
                    <th style="width: 25%">Name</th>
                    <th style="width: 20%">Email</th>
                    <th style="width: 15%">Phone</th>
                    <th style="width: 15%">Company</th>
                    <th style="width: 15%">Website</th>
                    <th style="width: 10%">Actions</th>
                  </tr>
                  <tr class="filter">
                    <th><input type="search" v-model="form.name" class="form-control-sm form-filter" /></th>
                    <th><input type="search" v-model="form.email" class="form-control-sm form-filter" /></th>
                    <th><input type="search" v-model="form.phone" class="form-control-sm form-filter" /></th>
                    <th><input type="search" v-model="form.company" class="form-control-sm form-filter" /></th>
                    <th><input type="search" v-model="form.website" class="form-control-sm form-filter" /></th>
                    <th>
                      <button class="btn btn-brand btn-sm" @click="search">
                        <i class="la la-search"></i> Search
                      </button>
                      <button class="btn btn-secondary btn-sm ml-1" @click="resetSearch">
                        <i class="la la-close"></i> Reset
                      </button>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="lead in props.leads.data" :key="lead.id" :class="{ 'table-success': isNewLead(lead) }">
                    <td>
                      <div class="kt-user-card-v2">
                        <div class="kt-user-card-v2__pic">
                          <div class="kt-badge kt-badge--xl kt-badge--success">
                            <span>{{ lead?.name?.substr(0, 1) || lead?.email?.substr(0, 1) }}</span>
                          </div>
                        </div>
                        <div class="kt-user-card-v2__details">
                          <span class="kt-user-card-v2__name">
                            {{ lead?.name || 'Lead from ' + lead?.company }}
                            <span v-if="isNewLead(lead)" class="badge badge-success ml-1">NEW</span>
                          </span>
                          <small class="kt-user-card-v2__email">
                            Added {{ formatDate(lead?.created_at) }}
                          </small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span :class="{ 'text-success font-weight-bold': isNewLead(lead) }">
                        {{ lead?.email }}
                      </span>
                    </td>
                    <td>
                      <span :class="{ 'text-success font-weight-bold': isNewLead(lead) }">
                        {{ lead?.phone }}
                      </span>
                    </td>
                    <td>{{ lead?.company }}</td>
                    <td>
                      <a v-if="lead?.website" :href="lead.website" target="_blank" class="kt-link">
                        {{ lead.website }}
                      </a>
                    </td>
                    <td>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                          <i class="la la-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                          <Link class="dropdown-item" :href="route('admin.editLead', lead?.id)">
                            <i class="la la-edit"></i> Edit
                          </Link>
                          <button class="dropdown-item" @click="deleteRecode(lead?.id)">
                            <i class="fa fa-trash"></i> Delete
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-sm-12" v-if="leads.total == 0">
              <div class="no_data text-center">
                <h3>No Leads Found</h3>
                <p>Start by adding leads manually or use the web scraper above.</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pagination -->
        <div class="row">
          <div class="col-sm-12 col-md-5">
            <div class="dataTables_info">
              Showing {{ leads.from }} to {{ leads.to }} of {{ leads.total }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-7">
            <div class="float-right">
              <Bootstrap4Pagination :data="leads" @pagination-change-page="ListHelper.setPageNum" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref, reactive } from "vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import ListHelper from "@/helpers/ListHelper";
import perPageDropdown from "@/components/PerpageDropdown.vue";
import { pickBy } from "lodash";

const props = defineProps({
  leads: Object,
});

const showScraper = ref(false);
const scrapeLoading = ref(false);
const bulkLoading = ref(false);
const scrapeResults = ref(null);
const bulkResults = ref(null);
const bulkWebsites = ref('');
const newLeadIds = ref([]);

const scraperForm = reactive({
  website: '',
  autoCreateLeads: true
});

const params = () => new URLSearchParams(window.location.search);

const form = reactive({
  name: params().get("name") || null,
  email: params().get("email") || null,
  phone: params().get("phone") || null,
  company: params().get("company") || null,
  website: params().get("website") || null,
});

onMounted(() => {
  emit.emit("pageName", "Lead Management", [
    {
      title: "Lead List",
      routeName: "admin.leads",
    },
  ]);

  emit.on("deleteConfirm", function (arg1) {
    deleteConfirm(arg1);
  });
});

onUnmounted(() => {
  emit.off("deleteConfirm");
});

const resetSearch = () => {
  router.visit(route("admin.leads"), { method: "get" });
};

const search = () => {
  form.perPage = params().get("perPage");
  router.visit(route("admin.leads"), {
    method: "get",
    data: pickBy(form),
    preserveScroll: true,
  });
};

const deleteRecode = (id) => {
  sw.confirm("deleteConfirm", id);
};

const deleteConfirm = (id) => {
  router.delete(route("admin.deleteLead", id));
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const isNewLead = (lead) => {
  const leadDate = new Date(lead.created_at);
  const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000);
  return leadDate > fiveMinutesAgo;
};

// Web Scraping Functions
const scrapeWebsite = async () => {
  scrapeLoading.value = true;
  scrapeResults.value = null;
  
  try {
    const response = await fetch('/admin/scrape', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        website: scraperForm.website,
        auto_create_leads: scraperForm.autoCreateLeads
      })
    });
    
    scrapeResults.value = await response.json();
    
    if (scrapeResults.value.success && scrapeResults.value.leads_created) {
      // Refresh the leads list immediately
      setTimeout(() => {
        router.reload({ only: ['leads'] });
      }, 1000);
    }
  } catch (error) {
    scrapeResults.value = { success: false, message: 'Network error occurred' };
  } finally {
    scrapeLoading.value = false;
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
    
    // Refresh leads list after bulk scraping
    setTimeout(() => {
      router.reload({ only: ['leads'] });
    }, 1000);
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

.table-success {
  background-color: #d4edda !important;
}

.text-success {
  color: #28a745 !important;
}

.font-weight-bold {
  font-weight: 600 !important;
}
</style>