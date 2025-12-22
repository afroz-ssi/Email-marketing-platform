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
              <WebScraper @leads-created="refreshLeads" />
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
                  <tr v-for="lead in props.leads.data" :key="lead.id">
                    <td>
                      <div class="kt-user-card-v2">
                        <div class="kt-user-card-v2__pic">
                          <div class="kt-badge kt-badge--xl kt-badge--success">
                            <span>{{ lead?.name?.substr(0, 1) }}</span>
                          </div>
                        </div>
                        <div class="kt-user-card-v2__details">
                          <span class="kt-user-card-v2__name">{{ lead?.name }}</span>
                          <small class="kt-user-card-v2__email">
                            Added {{ formatDate(lead?.created_at) }}
                          </small>
                        </div>
                      </div>
                    </td>
                    <td>{{ lead?.email }}</td>
                    <td>{{ lead?.phone }}</td>
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
import { onMounted, onUnmounted, ref } from "vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import ListHelper from "@/helpers/ListHelper";
import perPageDropdown from "@/components/PerpageDropdown.vue";
import WebScraper from "@/components/WebScraperOptimized.vue";
import { reactive } from "vue";
import { pickBy } from "lodash";

const props = defineProps({
  leads: Object,
});

const showScraper = ref(false);
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

const refreshLeads = () => {
  router.reload({ only: ['leads'] });
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};
</script>