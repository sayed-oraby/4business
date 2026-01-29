var b=(function(){var l,t=window.PostModule,e=document.getElementById("post-filter-form"),d=document.querySelector("[data-filter-apply]"),o=document.querySelector("[data-filter-reset]"),p=function(){return e?{status:e.status?.value||"",category_id:e.category_id?.value||"",post_type_id:e.post_type_id?.value||"",package_id:e.package_id?.value||"",is_paid:e.is_paid?.value||"",gender:e.gender?.value||"",city_id:e.city_id?.value||"",date_from:e.date_from?.value||"",date_to:e.date_to?.value||""}:{}},u=function(){l=$("#posts-table").DataTable({searchDelay:500,processing:!0,serverSide:!0,ajax:{url:t.routes.index,type:"GET",data:function(n){var s=p();$.extend(n,s)}},columns:[{data:null},{data:null},{data:null},{data:null},{data:"status"},{data:"created_at"},{data:null}],columnDefs:[{targets:0,render:function(n,s,a){var r=a.title[t.locale]||a.title.en||a.title,i="";return a.cover_image_url?i=`
                                <div class="symbol symbol-50px symbol-2by3 me-3">
                                    <div class="symbol-label" style="background-image: url('${a.cover_image_url}'); background-size: cover; background-position: center;"></div>
                                </div>
                            `:i=`
                                <div class="symbol symbol-50px symbol-2by3 me-3">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-briefcase fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                            `,`
                            <div class="d-flex align-items-center">
                                ${i}
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-900">${r}</span>
                                </div>
                            </div>
                        `}},{targets:1,render:function(n,s,a){return a.user?.name||"-"}},{targets:2,render:function(n,s,a){return a.post_type&&(a.post_type.name[t.locale]||a.post_type.name.en)||"-"}},{targets:3,render:function(n,s,a){var r="-";a.package&&a.package.title&&(r=a.package.title[t.locale]||a.package.title.en||a.package.title);var i=a.package&&parseFloat(a.package.price||0)>0,c="";return i?a.is_paid?c=`<span class="badge badge-success ms-2">${t.labels.paid}</span>`:c=`<span class="badge badge-danger ms-2">${t.labels.unpaid}</span>`:c=`<span class="badge badge-light-primary ms-2">${t.labels.free}</span>`,r+c}},{targets:4,render:function(n,s,a){var r={pending:"badge-light-warning",approved:"badge-light-success",rejected:"badge-light-danger",expired:"badge-light-secondary",awaiting_payment:"badge-light-info",payment_failed:"badge-light-danger",active:"badge-light-success",inactive:"badge-light-secondary"}[a.status]||"badge-light-secondary",i=t.statuses[a.status]||a.status;return`<span class="badge ${r}">${i}</span>`}},{targets:5,render:function(n){return moment(n).format("YYYY-MM-DD")}},{targets:-1,data:null,orderable:!1,className:"text-end",render:function(n,s,a){return`
                            <a href="${t.routes.show.replace("__ID__",a.id)}" class="btn btn-icon btn-active-light-primary w-30px h-30px me-3">
                                <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </a>
                            <button class="btn btn-icon btn-active-light-danger w-30px h-30px" data-post-action="delete" data-id="${a.id}">
                                <i class="ki-duotone ki-trash fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </button>
                        `}}]}),l.$,$("#post-search").on("keyup",function(){l.search(this.value).draw()})},g=function(){d?.addEventListener("click",()=>{l.ajax.reload(),bootstrap.Offcanvas.getInstance(document.getElementById("postFiltersCanvas"))?.hide()}),o?.addEventListener("click",()=>{e?.reset(),l.ajax.reload()})},f=function(){$(document).on("click",'[data-post-action="delete"]',function(){var n=$(this).data("id");Swal.fire({text:t.confirm.deleteMessage,icon:"warning",showCancelButton:!0,buttonsStyling:!1,confirmButtonText:t.confirm.confirm,cancelButtonText:t.confirm.cancel,customClass:{confirmButton:"btn fw-bold btn-danger",cancelButton:"btn fw-bold btn-active-light-primary"}}).then(function(s){s.value&&axios.delete(t.routes.destroy.replace("__ID__",n)).then(function(a){Swal.fire({text:a.data.message,icon:"success",buttonsStyling:!1,confirmButtonText:"Ok, got it!",customClass:{confirmButton:"btn fw-bold btn-primary"}}).then(function(){l.ajax.reload()})}).catch(function(a){Swal.fire({text:a.response?.data?.message||"Error deleting post.",icon:"error",buttonsStyling:!1,confirmButtonText:"Ok, got it!",customClass:{confirmButton:"btn fw-bold btn-primary"}})})})})};return{init:function(){u(),g(),f()}}})();KTUtil.onDOMContentLoaded(function(){b.init()});
