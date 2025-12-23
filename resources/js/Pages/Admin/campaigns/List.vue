<template lang="">
  <div>
    <Head title="Client Lists" />
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
                <button
                  @click="resetAllAccounts"
                  class="text-white btn btn-warning mr-2"
                >
                  <i class="la la-refresh"></i>Reset All Accounts
                </button>
                <Link
                  :href="route('admin.createCampaign')"
                  class="btn btn-primary"
                  ><i class="la la-plus"></i>Create Campaign</Link
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
                      Campaign Name
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
                      aria-label=" sort column ascending"
                    >
                      Message
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                        @click="ListHelper.sortBy('message')"
                      ></i>
                    </th>

                    <th
                      tabindex="0"
                      aria-controls="kt_table_1"
                      rowspan="1"
                      colspan="1"
                      style="width: 25%"
                      aria-label=" sort column ascending"
                    >
                      Send Mail
                      <i
                        class="fa fa-fw fa-sort pull-right"
                        style="cursor: pointer"
                      ></i>
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
                    <th style="width: 50%">
                      <input
                        type="search"
                        v-model="form.message"
                        placeholder=""
                        autocomplete="off"
                        class="form-control-sm form-filter"
                      />
                    </th>
                    <th></th>

                    <th style="width: 20%">
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
                    v-for="campaign in props.campaigns.data"
                    :key="campaign.id"
                  >
                    <td class="sorting_1" tabindex="0">
                      <div class="kt-user-card-v2">
                        <div class="kt-user-card-v2__pic">
                          <div
                            class="kt-badge kt-badge--xl kt-badge--success"
                            :class="ListHelper.getRandomVal()"
                          >
                            <span>{{ campaign?.name?.substr(0, 1) }}</span>
                          </div>
                        </div>
                        <div class="kt-user-card-v2__details">
                          <span class="kt-user-card-v2__name">{{
                            campaign?.name
                          }}</span>
                          <a href="#" class="kt-user-card-v2__email kt-link"
                            >Member since
                            {{
                              ListHelper.dateFormat(
                                campaign?.created_at,
                                "MMM DD, YYYY hh:mm A"
                              )
                            }}</a
                          >
                        </div>
                      </div>
                    </td>
                    <td>{{ campaign?.message }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <select
                          v-model="selectedAccounts[campaign.id]"
                          class="form-control form-control-sm mr-2"
                          style="width: 150px"
                        >
                          <option
                            v-for="account in outlookAccounts"
                            :key="account.id"
                            :value="account.id"
                            :disabled="
                              account.sent_today >= account.daily_limit
                            "
                          >
                            {{ account.email }} ({{ account.sent_today }}/{{
                              account.daily_limit
                            }})
                          </option>
                        </select>
                        <button
                          class="btn btn-brand kt-btn btn-sm kt-btn--icon button-fx"
                          @click="sendMail(campaign?.id)"
                        >
                          <span>
                            <i class="la la-send"></i>
                            <span>Send</span>
                          </span>
                        </button>
                      </div>
                    </td>
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
                            :href="route('admin.editCampaign', campaign?.id)"
                            ><i class="la la-edit"></i> Edit</Link
                          >
                          <button
                            href="#"
                            class="dropdown-item"
                            @click="deleteRecode(campaign?.id)"
                          >
                            <i class="fa fa-trash"></i> Delete
                          </button>
                          <Link
                            class="dropdown-item"
                            :href="route('admin.outlookAccounts')"
                          >
                            <i class="la la-refresh"></i> Manage Outlook
                            Accounts
                          </Link>
                        </div>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-sm-12" v-if="campaigns?.total == 0">
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
              Showing {{ campaigns?.from || 0 }} to {{ campaigns?.to || 0 }} of
              {{ campaigns?.total || 0 }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-7">
            <div class="float-right">
              <Bootstrap4Pagination
                :data="campaigns"
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
  campaigns: Object,
  outlookAccounts: Array,
  defaultAccountId: Number,
});

const params = () => new URLSearchParams(window.location.search);

const form = reactive({
  name: params().get("name") || null,
  message: params().get("message") || null,
});

const selectedAccounts = reactive({});

// Set default account for all campaigns
if (props.defaultAccountId && props.campaigns?.data) {
  props.campaigns.data.forEach((campaign) => {
    selectedAccounts[campaign.id] = props.defaultAccountId;
  });
}

onMounted(() => {
  emit.emit("pageName", "Campaign Management", [
    {
      title: "Campaign List",
      routeName: "admin.campaigns",
    },
  ]);

  emit.on("deleteConfirm", function (arg1) {
    deleteConfirm(arg1);
  });

  emit.on("changeStatusConfirm", function (arg1) {
    changeStatusConfirm(arg1);
  });
});

onUnmounted(() => {
  emit.off("changeStatusConfirm");
  emit.off("deleteConfirm");
});

const resetSearch = () => {
  router.visit(route("admin.campaigns"), {
    method: "get",
  });
};

const search = () => {
  form.perPage = params().get("perPage");
  router.visit(route("admin.campaigns"), {
    method: "get",
    data: pickBy(form),
    preserveScroll: true,
  });
};

const deleteRecode = (id) => {
  sw.confirm("deleteConfirm", id);
};

const deleteConfirm = (id) => {
  router.delete(route("admin.deleteCampaign", id));
};

const sendMail = (id) => {
  const accountId = selectedAccounts[id] || null;
  router.post(route("admin.sendCampaign", id), {
    outlook_account_id: accountId,
  });
};

const resetAllAccounts = () => {
  router.post(route("admin.resetAllAccounts"));
};
</script>
<style lang=""></style>
