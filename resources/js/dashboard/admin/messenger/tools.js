function showAdminToast(message, type = 'success')
{
    const toast =
        document.getElementById('admin-toast');

    const text =
        document.getElementById('admin-toast-message');

    const icon =
        document.getElementById('admin-toast-icon');


    if (!toast || !text) {
        return;
    }


    text.innerText = message;


    if (icon) {

        icon.classList.remove(
            'bg-green-500',
            'bg-red-500'
        );


        if (type === 'error') {

            icon.classList.add(
                'bg-red-500'
            );

        } else {

            icon.classList.add(
                'bg-green-500'
            );

        }

    }


    toast.classList.remove(
        'hidden'
    );


    setTimeout(() => {

        toast.classList.add(
            'hidden'
        );

    }, 3500);
}




document.addEventListener(
    'DOMContentLoaded',
    () => {


        /*
        |--------------------------------------------------------------------------
        | Tools collapse
        |--------------------------------------------------------------------------
        */

        const tools =
        document.getElementById(
            'message-center-tools'
        );


   

        console.log('TOOLS:', tools);



    if (tools) {


    console.log(
    'STATISTICS URL:',
    tools.dataset.statisticsUrl
);


   fetch(
    tools.dataset.statisticsUrl,
    {
        headers: {
            'Accept': 'application/json'
        }
    }
)
.then(response => response.json())
.then(data => {

     console.log(
        'STATISTICS:',
        data
    );

    document.getElementById(
        'conversations-count'
    ).innerText = data.total ?? 0;

    document.getElementById(
        'empty-conversations-count'
    ).innerText = data.empty ?? 0;

})
.catch(error => {

    console.error(
        'STATISTICS ERROR:',
        error
    );

});

}


        const toggle =
            document.getElementById(
                'message-center-tools-toggle'
            );


        const content =
            document.getElementById(
                'message-center-tools-content'
            );


        const arrow =
            document.getElementById(
                'tools-arrow-icon'
            );



        if (toggle && content) {


            toggle.onclick = () => {


                const opened =
                    !content.classList.contains(
                        'hidden'
                    );


                if (opened) {


                    content.classList.add(
                        'hidden'
                    );


                    arrow?.classList.remove(
                        'rotate-180'
                    );


                } else {


                    content.classList.remove(
                        'hidden'
                    );


                    arrow?.classList.add(
                        'rotate-180'
                    );

                }


            };

        }




        /*
        |--------------------------------------------------------------------------
        | Delete empty conversations
        |--------------------------------------------------------------------------
        */


        const button =
            document.getElementById(
                'delete-empty-conversations'
            );



        if (!button) {
            return;
        }



        button.addEventListener(
            'click',
            () => {



                if (!window.confirmModal) {

                    console.error(
                        'Confirm modal is not initialized'
                    );

                    return;

                }



                window.confirmModal.open({

                    type: 'danger',

                    title:
                        'Delete empty conversations',


                    description:
                        'Permanent cleanup action',


                    message:
                        'Are you sure you want to delete all conversations without messages? This action cannot be undone.',


                    cancelText:
                        'Cancel',


                    confirmText:
                        'Delete',



                    onConfirm:
                        async () => {


                            button.disabled = true;


                            button.classList.add(
                                'opacity-50',
                                'cursor-not-allowed'
                            );



                            try {


                                const csrf =
                                    document.querySelector(
                                        'meta[name="csrf-token"]'
                                    );



                                const response =
                                    await fetch(
                                        button.dataset.url,
                                        {

                                            method: 'DELETE',


                                            headers: {

                                                'X-CSRF-TOKEN':
                                                    csrf
                                                    ? csrf.content
                                                    : '',


                                                'Accept':
                                                    'application/json',

                                            }

                                        }
                                    );



                                if (!response.ok) {

                                    throw new Error(
                                        'Delete failed'
                                    );

                                }




                                const data =
                                    await response.json();




                                showAdminToast(
                                    `Deleted ${data.count} empty conversations`
                                );




                                setTimeout(
                                    () => {

                                        window.location.reload();

                                    },
                                    1200
                                );




                            } catch(error) {



                                console.error(error);



                                showAdminToast(
                                    'Error deleting conversations',
                                    'error'
                                );



                            } finally {



                                button.disabled = false;


                                button.classList.remove(
                                    'opacity-50',
                                    'cursor-not-allowed'
                                );


                            }


                        }

                });


            }
        );


    }
);