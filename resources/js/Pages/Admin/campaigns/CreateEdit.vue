<template lang="">
  <div>
    <Head title="Create Campaign" v-if="!props.campaign" />
    <Head title="Edit Campaign" v-if="props.campaign" />

    <div class="kt-portlet kt-portlet--mobile">
      <div class="kt-portlet__body">
        <form @submit.prevent="submit">
          <div class="form-group validated row" v-auto-animate>
            <div class="form-group col-lg-12">
              <label for="name"
                >Campaign Name <span class="text-danger">*</span></label
              >
              <input
                type="text"
                id="name"
                v-model="form.name"
                @input="allowOnlyLetters('name', $event)"
                maxlength="100"
                class="form-control border-gray-200"
                placeholder="Campaign Name"
              />
              <span class="text-danger" v-if="form.errors.name">{{
                form.errors.name
              }}</span>
            </div>

            <div class="form-group col-lg-12">
              <label for="message"
                >Message <span class="text-danger">*</span></label
              >
              <textarea
                id="message"
                v-model="form.message"
                class="form-control border-gray-200"
                placeholder="Message"
                maxlength="500"
                rows="10"
              ></textarea>
              <span class="text-danger" v-if="form.errors.message">{{
                form.errors.message
              }}</span>
            </div>
          </div>
          <div class="kt-portlet__foot">
            <div class="kt-form__actions">
              <div class="row">
                <div class="pr-2">
                  <submit-button
                    :disabled="form.processing"
                    :isLoading="form.processing"
                    >Submit</submit-button
                  >
                </div>
                <div>
                  <Link
                    :href="route('admin.users')"
                    class="btn btn-info btn-secondary"
                    >Cancel</Link
                  >
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, watch, ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import SubmitButton from "@/components/SubmitButton.vue";

const props = defineProps({
  errors: Object,
  campaign: Object,
});

const form = useForm({
  name: props.campaign?.name || null,
  message: props.campaign?.message || null,
});

onMounted(() => {
  const pageTitle = props.user ? "Edit Client" : "Create Client";
  emit.emit("pageName", "Campaign Management", [
    {
      title: "Campaign List",
      routeName: "admin.campaigns",
    },
    {
      title: pageTitle,
      routeName: "",
    },
  ]);
});

const submit = () => {
  if (props.campaign) {
    form.post(route("admin.editCampaign", props.campaign.id));
  } else {
    form.post(route("admin.createCampaign"));
  }
};

const allowOnlyLetters = (param, e) => {
  let inputValue = e.target.value.replace(/[^a-zA-Z\s]/g, "");
  if (inputValue !== "" && inputValue !== "0") {
    form[param] = inputValue;
  } else {
    form[param] = "";
  }
};

const emailValidate = (param, e) => {
  let inputValue = e.target.value.replace(/[^a-zA-Z0-9._%#.+-@]/g, "");
  if (inputValue !== "" && inputValue !== "0") {
    form[param] = inputValue;
  } else if (
    !inputValue.includes("@") ||
    !inputValue.split("@")[1].includes(".")
  ) {
    form.errors.email = "Invalid email formate";
  } else {
    form[param] = "";
  }
};
</script>
