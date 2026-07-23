export default class SupportRequestDrawer
{

    constructor(api, options = {})
    {
        this.api = api;

        this.onCreated = options.onCreated ?? null;

       this.drawer =
    document.getElementById(
        'create-support-request-drawer'
    );


        if (!this.drawer) {
            return;
        }


        this.bind();

    }



    bind()
    {

        this.drawer
            .querySelectorAll(
                '[data-close-support-request]'
            )
            .forEach(button => {

                button.onclick = () => {

                    this.close();

                };

            });



        const submit =
            document.getElementById(
                'submit-support-request'
            );


        if (submit) {

           submit.onclick = async () => {
    try {
        await this.submit();
    } catch (e) {
        console.error(e);
    }
};

        }

    }



    open()
    {
        this.drawer
            ?.classList
            .remove('hidden');
    }



    close()
    {
        this.drawer
            ?.classList
            .add('hidden');
    }




    async submit() {

    console.group('=== Support Request ===');

    try {

        const subject =
            document.getElementById('support-subject').value;

        const category =
            document.getElementById('support-category').value;

        const description =
            document.getElementById('support-description').value;

        console.log('Request payload:', {
            subject,
            category,
            description,
        });

        const response = await this.api.request(
            '/dashboard/support/request',
            'POST',
            {
                subject,
                category,
                description,
            }
        );

        console.log('API response:', response);

        this.close();

        if (this.onCreated) {
            await this.onCreated(response);
        }

    } catch (error) {

        console.error('Support request failed:', error);

        if (error.response) {
            console.error('Response:', error.response);
        }

        throw error;

    } finally {

        console.groupEnd();

    }
}



}