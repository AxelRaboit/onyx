import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import AppButton from '@/components/ui/AppButton.vue';

describe('AppButton', () => {
    it('renders a button element', () => {
        const wrapper = mount(AppButton);

        expect(wrapper.element.tagName).toBe('BUTTON');
    });

    it('uses type="button" by default', () => {
        const wrapper = mount(AppButton);

        expect(wrapper.attributes('type')).toBe('button');
    });

    it('applies a custom type', () => {
        const wrapper = mount(AppButton, { props: { type: 'submit' } });

        expect(wrapper.attributes('type')).toBe('submit');
    });

    it('renders slot content', () => {
        const wrapper = mount(AppButton, { slots: { default: 'Enregistrer' } });

        expect(wrapper.text()).toBe('Enregistrer');
    });

    it('applies primary variant classes by default', () => {
        const wrapper = mount(AppButton);

        expect(wrapper.classes()).toContain('bg-indigo-600');
    });

    it('applies danger variant classes', () => {
        const wrapper = mount(AppButton, { props: { variant: 'danger' } });

        expect(wrapper.classes()).toContain('bg-red-600');
    });

    it('applies secondary variant classes', () => {
        const wrapper = mount(AppButton, { props: { variant: 'secondary' } });

        expect(wrapper.classes()).toContain('bg-surface-3');
    });

    it('applies ghost variant classes', () => {
        const wrapper = mount(AppButton, { props: { variant: 'ghost' } });

        expect(wrapper.classes()).toContain('bg-transparent');
    });

    it('is disabled when disabled prop is true', () => {
        const wrapper = mount(AppButton, { props: { disabled: true } });

        expect(wrapper.attributes('disabled')).toBeDefined();
    });

    it('is not disabled by default', () => {
        const wrapper = mount(AppButton);

        expect(wrapper.attributes('disabled')).toBeUndefined();
    });

    it('applies sm size classes', () => {
        const wrapper = mount(AppButton, { props: { size: 'sm' } });

        expect(wrapper.classes()).toContain('text-xs');
    });
});
