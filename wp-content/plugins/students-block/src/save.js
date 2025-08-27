import { useBlockProps } from '@wordpress/block-editor';

export default function StudentsDisplaySave() {
    const blockProps = useBlockProps.save();

    // This block uses dynamic rendering, so we return a simple div
    // The actual content will be rendered by the render_callback in the PHP file
    return (
        <div {...blockProps}>
            {/* Dynamic content will be rendered server-side */}
        </div>
    );
}
