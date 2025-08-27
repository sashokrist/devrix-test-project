import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { 
    PanelBody, 
    RangeControl, 
    SelectControl, 
    ToggleControl,
    Notice,
    TextControl
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

export default function StudentsDisplayEdit({ attributes, setAttributes }) {
    const {
        numberOfStudents,
        status,
        showSpecificStudent,
        specificStudentId,
        orderBy,
        order,
    } = attributes;

    const [students, setStudents] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    // Get students data for the dropdown
    useEffect(() => {
        if (window.studentsBlockData && window.studentsBlockData.students) {
            setStudents(window.studentsBlockData.students);
            setIsLoading(false);
        }
    }, []);

    const blockProps = useBlockProps();

    // Create options for student selection
    const studentOptions = [
        { label: __('Select a student...', 'students-block'), value: 0 },
        ...students.map(student => ({
            label: `${student.title} (${student.status === '1' ? __('Active', 'students-block') : __('Inactive', 'students-block')})`,
            value: student.id,
        })),
    ];

    // Create a preview of what the block will look like
    const renderPreview = () => {
        const statusText = status === 'active' ? __('Active', 'students-block') : 
                          status === 'inactive' ? __('Inactive', 'students-block') : 
                          __('All', 'students-block');
        
        return (
            <div className="students-block-preview">
                <div className="students-grid-preview">
                    {showSpecificStudent && specificStudentId > 0 ? (
                        // Show single student preview
                        <div className="student-card-preview">
                            <div className="student-image-preview">
                                <div className="placeholder-image"></div>
                            </div>
                            <div className="student-info-preview">
                                <h4 className="student-name-preview">
                                    {__('Selected Student', 'students-block')}
                                </h4>
                                <p className="student-details-preview">
                                    {__('Class/Grade:', 'students-block')} {__('Sample Grade', 'students-block')}
                                </p>
                                                                        <p className="student-details-preview">
                                            {__('Email:', 'students-block')} {__('student@example.com', 'students-block')}
                                        </p>
                                        <p className="student-details-preview">
                                            {__('Age:', 'students-block')} {__('16', 'students-block')}
                                        </p>
                                        <p className="student-details-preview">
                                            {__('School:', 'students-block')} {__('Sample School', 'students-block')}
                                        </p>
                                        <div className="status-indicator-preview active">
                                            {__('Active', 'students-block')}
                                        </div>
                            </div>
                        </div>
                    ) : (
                        // Show multiple students preview
                        <>
                            {[...Array(Math.min(numberOfStudents, 3))].map((_, index) => (
                                <div key={index} className="student-card-preview">
                                    <div className="student-image-preview">
                                        <div className="placeholder-image"></div>
                                    </div>
                                    <div className="student-info-preview">
                                        <h4 className="student-name-preview">
                                            {__('Student Name', 'students-block')} {index + 1}
                                        </h4>
                                        <p className="student-details-preview">
                                            {__('Class/Grade:', 'students-block')} {__('Sample Grade', 'students-block')}
                                        </p>
                                        <p className="student-details-preview">
                                            {__('Email:', 'students-block')} {__('student@example.com', 'students-block')}
                                        </p>
                                        <p className="student-details-preview">
                                            {__('Age:', 'students-block')} {__('16', 'students-block')}
                                        </p>
                                        <p className="student-details-preview">
                                            {__('School:', 'students-block')} {__('Sample School', 'students-block')}
                                        </p>
                                        <div className="status-indicator-preview active">
                                            {statusText}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {numberOfStudents > 3 && (
                                <div className="more-students-preview">
                                    <p>{__('+', 'students-block')} {numberOfStudents - 3} {__('more students', 'students-block')}</p>
                                </div>
                            )}
                        </>
                    )}
                </div>
                <div className="preview-footer">
                    <p>{__('This is a preview. The actual students will be displayed on the frontend.', 'students-block')}</p>
                </div>
            </div>
        );
    };

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Students Display Settings', 'students-block')} initialOpen={true}>
                    <ToggleControl
                        label={__('Show specific student', 'students-block')}
                        checked={showSpecificStudent}
                        onChange={(value) => {
                            setAttributes({ 
                                showSpecificStudent: value,
                                // Reset specific student ID when toggling off
                                specificStudentId: value ? specificStudentId : 0
                            });
                        }}
                        help={showSpecificStudent ? 
                            __('Display a single, specific student', 'students-block') : 
                            __('Display multiple students with filtering options', 'students-block')
                        }
                    />

                    {showSpecificStudent ? (
                        <div>
                            <SelectControl
                                label={__('Select Student', 'students-block')}
                                value={specificStudentId}
                                options={studentOptions}
                                onChange={(value) => setAttributes({ specificStudentId: parseInt(value) })}
                                disabled={isLoading}
                                help={isLoading ? 
                                    __('Loading students...', 'students-block') : 
                                    __('Choose which student to display', 'students-block')
                                }
                            />
                            {specificStudentId === 0 && (
                                <Notice status="warning" isDismissible={false}>
                                    {__('Please select a student to display', 'students-block')}
                                </Notice>
                            )}
                        </div>
                    ) : (
                        <>
                            <RangeControl
                                label={__('Number of students to show', 'students-block')}
                                value={numberOfStudents}
                                onChange={(value) => setAttributes({ numberOfStudents: value })}
                                min={1}
                                max={20}
                                step={1}
                                help={__('Choose how many students to display (1-20)', 'students-block')}
                            />

                            <SelectControl
                                label={__('Status filter', 'students-block')}
                                value={status}
                                options={[
                                    { label: __('Active students only', 'students-block'), value: 'active' },
                                    { label: __('Inactive students only', 'students-block'), value: 'inactive' },
                                    { label: __('All students', 'students-block'), value: 'all' },
                                ]}
                                onChange={(value) => setAttributes({ status: value })}
                                help={__('Filter students by their active/inactive status', 'students-block')}
                            />

                            <SelectControl
                                label={__('Order by', 'students-block')}
                                value={orderBy}
                                options={[
                                    { label: __('Name (A-Z)', 'students-block'), value: 'title' },
                                    { label: __('Date created', 'students-block'), value: 'date' },
                                    { label: __('Date modified', 'students-block'), value: 'modified' },
                                    { label: __('Menu order', 'students-block'), value: 'menu_order' },
                                ]}
                                onChange={(value) => setAttributes({ orderBy: value })}
                                help={__('Choose how to sort the students', 'students-block')}
                            />

                            <SelectControl
                                label={__('Order direction', 'students-block')}
                                value={order}
                                options={[
                                    { label: __('Ascending (A-Z, Oldest first)', 'students-block'), value: 'ASC' },
                                    { label: __('Descending (Z-A, Newest first)', 'students-block'), value: 'DESC' },
                                ]}
                                onChange={(value) => setAttributes({ order: value })}
                                help={__('Choose the sort direction', 'students-block')}
                            />
                        </>
                    )}
                </PanelBody>

                <PanelBody title={__('Block Information', 'students-block')} initialOpen={false}>
                    <div style={{ fontSize: '12px', color: '#666' }}>
                        <p><strong>{__('Current Settings:', 'students-block')}</strong></p>
                        <ul style={{ margin: '8px 0', paddingLeft: '16px' }}>
                            {showSpecificStudent ? (
                                <li>{__('Mode:', 'students-block')} {__('Single student', 'students-block')}</li>
                            ) : (
                                <>
                                    <li>{__('Students to show:', 'students-block')} {numberOfStudents}</li>
                                    <li>{__('Status filter:', 'students-block')} {
                                        status === 'active' ? __('Active only', 'students-block') :
                                        status === 'inactive' ? __('Inactive only', 'students-block') :
                                        __('All students', 'students-block')
                                    }</li>
                                    <li>{__('Order by:', 'students-block')} {
                                        orderBy === 'title' ? __('Name', 'students-block') :
                                        orderBy === 'date' ? __('Date created', 'students-block') :
                                        orderBy === 'modified' ? __('Date modified', 'students-block') :
                                        __('Menu order', 'students-block')
                                    }</li>
                                    <li>{__('Order:', 'students-block')} {
                                        order === 'ASC' ? __('Ascending', 'students-block') : __('Descending', 'students-block')
                                    }</li>
                                </>
                            )}
                        </ul>
                        <p style={{ marginTop: '12px', fontStyle: 'italic' }}>
                            {__('This block displays students from your Students custom post type with filtering and ordering options.', 'students-block')}
                        </p>
                    </div>
                </PanelBody>
            </InspectorControls>

            <div className="students-block-editor">
                <div className="students-block-editor-header">
                    <h3>{__('Students Display Block', 'students-block')}</h3>
                    <p className="description">
                        {showSpecificStudent ? (
                            specificStudentId > 0 ? 
                                __('Showing a specific student', 'students-block') :
                                __('Please select a student in the block settings', 'students-block')
                        ) : (
                            sprintf(
                                __('Showing %d students (filtered by: %s)', 'students-block'),
                                numberOfStudents,
                                status === 'active' ? __('Active', 'students-block') : 
                                status === 'inactive' ? __('Inactive', 'students-block') : 
                                __('All', 'students-block')
                            )
                        )
                    }
                    </p>
                </div>

                <div className="students-block-editor-preview">
                    {renderPreview()}
                </div>
            </div>
        </div>
    );
}
