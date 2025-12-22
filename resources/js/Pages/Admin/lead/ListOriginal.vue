<template>
  <div>
    <Head title="Lead Lists" />
    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__body">
        <div
          id="kt_table_1_wrapper"
          class="dataTables_wrapper dt-bootstrap4 no-footer"
        >
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <perPageDropdown />
            </div>
            <div class="col-sm-12 col-md-6">
              <div id="kt_table_1_filter" class="dataTables_filter">
                <Link :href="route('admin.createLead')" class="btn btn-primary"
                  ><i class="la la-plus"></i>Add New</Link
                >
              </div>
            </div>
          </div>
          <div class="row table-responsive table_fixed_width">
            <div class="col-sm-12">
              <table
                class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline"
                id="kt_table_1"
                role="grid"
                aria-describedby="kt_table_1_info"
                style="width: 1115px"
              >
                <thead>
                  <tr role="row">
                    <th
                      tabindex="0"
                      aria-controls="kt_table_1"
                      rowspan="1"
                      colspan="1"
                      style="width: 30%"
                      aria-sort="ascending"
                      aria-label="Agent: activate to sort column descending"
                    >
                      Name
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                        @click="ListHelper.sortBy('first_name')"
                      ></i>
                    </th>
                    <th
                      tabindex="0"
                      aria-controls="kt_table_1"
                      rowspan="1"
                      colspan="1"
                      style="width: 15%"
                      aria-label="Company Email: activate to sort column ascending"
                    >
                      Email
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                        @click="ListHelper.sortBy('email')"
                      ></i>
                    </th>
                    <th
                      tabindex="0"
                      aria-controls="kt_table_1"
                      rowspan="1"
                      colspan="1"
                      style="width: 15%"
                      aria-label="Company Agent: activate to sort column ascending"
                    >
                      Phone
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                        @click="ListHelper.sortBy('phone')"
                      ></i>
                    </th>

                    <th
                      tabindex="0"
                      aria-controls="kt_table_1"
                      rowspan="1"
                      colspan="1"
                      style="width: 15%"
                      aria-label="Company Agent: activate to sort column ascending"
                    >
                      Company
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                        @click="ListHelper.sortBy('company')"
                      ></i>
                    </th>

                    <th
                      tabindex="0"
                      aria-controls="kt_table_1"
                      rowspan="1"
                      colspan="1"
                      style="width: 15%"
                      aria-label="Company Agent: activate to sort column ascending"
                    >
                      Website
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                        @click="ListHelper.sortBy('website')"
                      ></i>
                    </th>

                    <th
                      class="align-center"
                      rowspan="1"
                      colspan="1"
                      style="width: 15%"
                      aria-label="Actions"
                    >
                      Actions
                    </th>
                  </tr>

                  <tr class="filter">
                    <th>
                      <input
                        type="search"
                        v-model="form.name"
                        placeholder=""
                        autocomplete="off"
                        class="form-control-sm form-filter"
                      />
                    </th>
                    <th>
                      <input
                        type="search"
                        v-model="form.email"
                        placeholder=""
                        autocomplete="off"
                        class="form-control-sm form-filter"
                      />
                    </th>
                    <th>
                      <input
                        type="search"
                        v-model="form.phone"
                        placeholder=""
                        autocomplete="off"
                        class="form-control-sm form-filter"
                      />
                    </th>

                    <th>
                      <input
                        type="search"
                        v-model="form.company"
                        placeholder=""
                        autocomplete="off"
                        class="form-control-sm form-filter"
                      />
                    </th>
                    <th>
                      <input
                        type="search"
                        v-model="form.website"
                        placeholder=""
                        autocomplete="off"
                        class="form-control-sm form-filter"
                      />
                    </th>
                    <th>
                      <div
                        class="row justify-content-center align-items-center"
                      >
                        <div class="col-md-6">
                          <button
                            class="btn btn-brand kt-btn btn-sm kt-btn--icon button-fx"
                            @click="search"
                          >
                            <span>
                              <i class="la la-search"></i>
                              <span>Search</span>
                            </span>
                          </button>
                        </div>
                        <div class="col-md-6">
                          <button
                            class="btn btn-secondary kt-btn btn-sm kt-btn--icon button-fx"
                            @click="resetSearch"
                          >
                            <span>
                              <i class="la la-close"></i>
                              <span>Reset</span>
                            </span>
                          </button>
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody v-auto-animate>
                  <tr
                    role="row"
                    class="odd"
                    v-for="lead in props.leads.data"
                    :key="lead.id"
                  >
                    <td class="sorting_1" tabindex="0">
                      <div class="kt-user-card-v2">
                        <div class="kt-user-card-v2__pic">
                          <div
                            class="kt-badge kt-badge--xl kt-badge--success"
                            :class="ListHelper.getRandomVal()"
                          >
                            <span>{{ lead?.name?.substr(0, 1) }}</span>
                          </div>
                        </div>
                        <div class="kt-user-card-v2__details">
                          <span class="kt-user-card-v2__name">{{
                            lead?.name
                          }}</span>
                          <a href="#" class="kt-user-card-v2__email kt-link"
                            >Member since
                            {{
                              ListHelper.dateFormat(
                                lead?.created_at,
                                "MMM DD, YYYY hh:mm A"
                              )
                            }}</a
                          >
                        </div>
                      </div>
                    </td>
                    <td>{{ lead?.email }}</td>
                    <td>{{ lead?.phone }}</td>
                    <td>{{ lead?.company }}</td>
                    <td>{{ lead?.website }}</td>
                    <td nowrap="" class="align-center">
                      <span class="dropdown">
                        <a
                          href="#"
                          class="btn btn-sm btn-clean btn-icon btn-icon-md"
                          data-toggle="dropdown"
                        >
                          <i class="la la-ellipsis-h"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                          <Link
                            class="dropdown-item"
                            :href="route('admin.editLead', lead?.id)"
                            ><i class="la la-edit"></i> Edit</Link
                          >
                          <button
                            href="#"
                            class="dropdown-item"
                            @click="deleteRecode(lead?.id)"
                          >
                            <i class="fa fa-trash"></i> Delete
                          </button>
                        </div>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-sm-12" v-if="leads.total == 0">
              <div class="no_data text-center">
                <h3>No Data Found</h3>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 col-md-5">
            <div
              class="dataTables_info"
              id="kt_table_1_info"
              role="status"
              aria-live="polite"
            >
              Showing {{ leads.from }} to {{ leads.to }} of
              {{ leads.total }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-7">
            <div class="float-right">
              <Bootstrap4Pagination
                :data="leads"
                :limit="2"
                @pagination-change-page="ListHelper.setPageNum"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import { onMounted, onUnmounted } from "vue";
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import ListHelper from "@/helpers/ListHelper";
import perPageDropdown from "@/components/PerpageDropdown.vue";
import { reactive } from "vue";
import { pickBy } from "lodash";

const props = defineProps({
  leads: Object,
});

const params = () => new URLSearchParams(window.location.search);

const form = reactive({
  name: params().get("name") || null,
  email: params().get("email") || null,
  phone: params().get("phone") || null,
  company: params().get("company") || null,
  website: params().get("website") || null,
  active: params().get("active") || "",
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
  router.visit(route("admin.leads"), {
    method: "get",
  });
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
</script>
<style lang=""></style>
