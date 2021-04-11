<div class="" >

    <div class="rounded-lg shadow-lg bg-white max-w-screen overflow-x-scroll">
        <div class="flex flex-col">

            <form class="" x-data="deleteRecord()" @submit.prevent="submitDelete" autocomplete="off">

            <div class="table align-middle min-w-full">
                <div class="table-row divide-x divide-gray-200">
        
                    <div class="relative table-cell h-12 overflow-hidden align-top">
                    
                        <button class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none flex justify-center">
                            <span class="inline ">{{ __('Type') }}</span>
                        </button>

                    </div>

                    <div class="relative table-cell h-12 overflow-hidden align-top">
                    
                        <button class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none flex justify-center">
                            <span class="inline ">{{ __('Hostname') }}</span>
                        </button>

                    </div>


                    <div class="relative table-cell h-12 overflow-hidden align-top">
                    
                        <button class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none flex justify-center">
                            <span class="inline ">{{ __('Value') }}</span>
                        </button>

                    </div>

                    <div class="relative table-cell h-12 overflow-hidden align-top">
                    
                        <button class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none flex justify-center">
                            <span class="inline ">{{ __('TTL (seconds)') }}</span>
                        </button>

                    </div>

                    <div class="relative table-cell h-12 overflow-hidden align-top">
                    
                        <button class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none flex justify-center">
                            <span class="inline ">{{ __('Actions') }}</span>
                        </button>

                    </div>

                </div>
                
                <template x-if="items" x-for=" item in items " :key=" item ">
                    
                    <div class="table-row p-1 divide-x divide-gray-100">

                            <div x-text=" item.type " class="px-6 py-2 whitespace-no-wrap text-sm leading-5 text-gray-600 table-cell text-left"></div>

                            <div x-text=" item.hostname " class="px-6 py-2 whitespace-no-wrap text-sm leading-5 text-gray-600 table-cell text-left"></div>

                            <div x-text=" item.value " class="px-6 py-2 whitespace-no-wrap text-sm leading-5 text-gray-600 table-cell text-left"></div>
                            
                            <div x-text=" item.ttl " class="px-6 py-2 whitespace-no-wrap text-sm leading-5 text-gray-600 table-cell text-left"></div>
        
                            <div class="px-6 py-2 whitespace-no-wrap text-sm leading-5 text-gray-600 table-cell text-left">
                                <template x-if="item.type!=='NS'">
                                    <button @click=" recordData.type = item.type ,recordData.ttl = item.ttl ,recordData.value = item.value, recordData.hostname = item.hostname " x-text="buttonLabel" :disabled="loading" class="btn rounded text-white bg-red-500 py-2 px-3"></button>
                                </template>                           
                            </div>
                            
                        </div>
                    
                </template>
                
                <template x-if="!items" x-for=" item in items " :key=" item ">
                    <p class="p-3 text-lg text-gray-500">
                        {{__('No data was found.')}}
                    </p>
                </template>

                
        </div>
    </form>
        
        </div>
    </div>

</div>
<script>

    function deleteRecord() {
        
      return {
            items: {!! json_encode($records) !!},
            recordData: {
                hostname: '',
                value: '',
                ttl: '',
                type: '',
                nonce: wpbuilderpro.nonce,
                action: 'record_delete'
            },
            message: '',
            loading: false,
            buttonLabel: 'Delete Record',
  
      submitDelete() {
       
            var body = new URLSearchParams( this.recordData );

            this.buttonLabel = 'Processing...'
            this.loading = true;
            this.message = ''
            
            fetch( wpbuilderpro.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            })
            .then(function(data) {
                return data.json();
            })
            .then(function (response) {
                
                if(response.message){
                    message = response.message;
                }
               
            })
            .catch(() => {
                this.message = 'Ooops! Something went wrong!';
            })
            .finally(() => {
                location.reload();
                if(message){
                    this.message = message;
                }
         
                this.loading = false;
                this.buttonLabel = 'Delete Record';
                

            })
      }
      }
  }
  </script>