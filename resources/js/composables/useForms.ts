import { ref, type Ref } from 'vue';
import { isEmpty, mapValues } from 'lodash';

export function useForms<T extends Record<string, any>>(initialFields: T = {} as T) {
    const fields: Ref<T> = ref(initialFields) as Ref<T>;
    const errors = ref<{ errors?: Record<string, string[]> }>({});

    const setForm = (value: Partial<T> = {} as Partial<T>) => {
        fields.value = { ...fields.value, ...value };
    };

    const resetForm = () => {
        fields.value = mapValues(fields.value, () => null) as T;
    };

    const setErrors = (value: { errors?: Record<string, string[]> } = {}) => {
        errors.value = value;
    };

    const getError = (key: string): string | undefined => {
        if (isEmpty(errors.value) || errors.value.errors?.[key] === undefined) {
            return;
        }

        return errors.value.errors[key][0];
    };

    const resetErrors = () => {
        setErrors({});
    };

    return { fields, setForm, resetForm, errors, setErrors, getError, resetErrors };
}