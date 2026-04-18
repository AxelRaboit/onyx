import AppBadge from '@/components/ui/AppBadge.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppInput from '@/components/form/AppInput.vue';
import AppModal from '@/components/ui/AppModal.vue';
import AppPageHeader from '@/components/ui/AppPageHeader.vue';
import AppPagination from '@/components/ui/AppPagination.vue';
import AppTooltip from '@/components/ui/AppTooltip.vue';
import ConfirmModal from '@/components/ui/ConfirmModal.vue';
import DeleteButton from '@/components/ui/DeleteButton.vue';
import EditButton from '@/components/ui/EditButton.vue';
import EmptyState from '@/components/ui/EmptyState.vue';
import FilterSelect from '@/components/form/FilterSelect.vue';
import FormField from '@/components/form/FormField.vue';
import FormSection from '@/components/ui/FormSection.vue';
import InputError from '@/components/form/InputError.vue';
import InputLabel from '@/components/form/InputLabel.vue';
import SearchInput from '@/components/form/SearchInput.vue';
import SelectInput from '@/components/form/SelectInput.vue';
import StatCard from '@/components/ui/StatCard.vue';
import TextInput from '@/components/form/TextInput.vue';

export default {
    install(app) {
        app.component('AppBadge', AppBadge);
        app.component('AppButton', AppButton);
        app.component('AppInput', AppInput);
        app.component('AppModal', AppModal);
        app.component('AppPageHeader', AppPageHeader);
        app.component('AppPagination', AppPagination);
        app.component('AppTooltip', AppTooltip);
        app.component('ConfirmModal', ConfirmModal);
        app.component('DeleteButton', DeleteButton);
        app.component('EditButton', EditButton);
        app.component('EmptyState', EmptyState);
        app.component('FilterSelect', FilterSelect);
        app.component('FormField', FormField);
        app.component('FormSection', FormSection);
        app.component('InputError', InputError);
        app.component('InputLabel', InputLabel);
        app.component('SearchInput', SearchInput);
        app.component('SelectInput', SelectInput);
        app.component('StatCard', StatCard);
        app.component('TextInput', TextInput);
    },
};
