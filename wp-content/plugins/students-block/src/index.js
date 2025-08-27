import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import StudentsDisplayEdit from './edit';
import StudentsDisplaySave from './save';

registerBlockType('students-block/students-display', {
    title: __('Students Display', 'students-block'),
    description: __('Display students with filtering options.', 'students-block'),
    category: 'widgets',
    icon: 'groups',
    keywords: [
        __('students', 'students-block'),
        __('display', 'students-block'),
        __('list', 'students-block'),
    ],
    supports: {
        html: false,
        align: ['wide', 'full'],
    },
    attributes: {
        numberOfStudents: {
            type: 'number',
            default: 4,
        },
        status: {
            type: 'string',
            default: 'active',
        },
        showSpecificStudent: {
            type: 'boolean',
            default: false,
        },
        specificStudentId: {
            type: 'number',
            default: 0,
        },
        orderBy: {
            type: 'string',
            default: 'title',
        },
        order: {
            type: 'string',
            default: 'ASC',
        },
        className: {
            type: 'string',
            default: '',
        },
    },
    edit: StudentsDisplayEdit,
    save: StudentsDisplaySave,
});
