import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { 
    PanelBody, 
    RangeControl, 
    SelectControl, 
    ToggleControl
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
                <PanelBody title={__('Students Settings', 'students-block')} initialOpen={true}>
                    <ToggleControl
                        label={__('Show specific student', 'students-block')}
                        checked={showSpecificStudent}
                        onChange={(value) => setAttributes({ showSpecificStudent: value })}
                        help={showSpecificStudent ? __('Showing a specific student', 'students-block') : __('Showing multiple students', 'students-block')}
                    />

                    {showSpecificStudent ? (
                        <SelectControl
                            label={__('Select Student', 'students-block')}
                            value={specificStudentId}
                            options={studentOptions}
                            onChange={(value) => setAttributes({ specificStudentId: parseInt(value) })}
                            disabled={isLoading}
                        />
                    ) : (
                        <>
                            <RangeControl
                                label={__('Number of students to show', 'students-block')}
                                value={numberOfStudents}
                                onChange={(value) => setAttributes({ numberOfStudents: value })}
                                min={1}
                                max={20}
                                step={1}
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
                            />

                            <SelectControl
                                label={__('Order by', 'students-block')}
                                value={orderBy}
                                options={[
                                    { label: __('Name', 'students-block'), value: 'title' },
                                    { label: __('Date created', 'students-block'), value: 'date' },
                                    { label: __('Date modified', 'students-block'), value: 'modified' },
                                    { label: __('Menu order', 'students-block'), value: 'menu_order' },
                                ]}
                                onChange={(value) => setAttributes({ orderBy: value })}
                            />

                            <SelectControl
                                label={__('Order', 'students-block')}
                                value={order}
                                options={[
                                    { label: __('Ascending', 'students-block'), value: 'ASC' },
                                    { label: __('Descending', 'students-block'), value: 'DESC' },
                                ]}
                                onChange={(value) => setAttributes({ order: value })}
                            />
                        </>
                    )}
                </PanelBody>
            </InspectorControls>

            <div className="students-block-editor">
                <div className="students-block-editor-header">
                    <h3>{__('Students Display Block', 'students-block')}</h3>
                    <p className="description">
                        {showSpecificStudent 
                            ? __('Showing a specific student', 'students-block')
                            : sprintf(
                                __('Showing %d students (filtered by: %s)', 'students-block'),
                                numberOfStudents,
                                status === 'active' ? __('Active', 'students-block') : 
                                status === 'inactive' ? __('Inactive', 'students-block') : 
                                __('All', 'students-block')
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
