import{a as m}from"./index-B9ygI19o.js";document.addEventListener("DOMContentLoaded",()=>{const r=window.UserModule||{},x=$("#users-table"),v=window.hostUrl||document.body.dataset.hostUrl||"/metronic/",g=`${v.endsWith("/")?v:`${v}/`}media/avatars/blank.png`,B={required:["required"],email:["email"],unique:["unique","already been taken"],confirmed:["confirm","match"],same:["same","match"],min:["at least","minimum","min"]};if(!x.length)return;const o=r.routes,b=document.getElementById("user-filter-form"),E=document.querySelector("[data-filter-apply]"),S=document.querySelector("[data-filter-reset]"),C=$("#user-avatar-preview"),p=$('[name="remove_avatar"]');f(null);const u=x.DataTable({processing:!0,serverSide:!0,ajax:(e,t)=>{e.filters=M(),m.get(o.data,{params:e}).then(s=>t(s.data)).catch(()=>t({data:[],recordsTotal:0,recordsFiltered:0}))},columns:[{data:"id",orderable:!1,className:"text-center align-middle",render:e=>`
                    <div class="form-check form-check-sm form-check-custom">
                        <input class="form-check-input user-select" type="checkbox" value="${e}">
                    </div>
                `},{data:"name",className:"text-center align-middle",render:(e,t,s)=>{const a=s.email??"";return`
                        <div class="d-flex flex-column align-items-center text-center gap-2">
                            <div class="symbol symbol-40px users-table-avatar">
                                <span class="symbol-label bg-light" style="background-size: cover; background-position: center; background-image: url('${s.avatar??g}');"></span>
                            </div>
                            <span class="text-gray-900 fw-bold lh-sm">${e}</span>
                            <span class="text-muted fw-semibold fs-7">${a}</span>
                        </div>
                    `}},{data:"mobile",defaultContent:"—",render:e=>`<span class="text-gray-900 fw-semibold">${e??"—"}</span>`,className:"text-center align-middle"},{data:"roles",defaultContent:[],render:e=>{const t=Array.isArray(e)?e:e?[e]:[];return t.length?t.map(s=>`
                    <span class="badge badge-light-primary me-1 mb-1">${s}</span>
                `).join(""):'<span class="text-muted">—</span>'},className:"text-center align-middle"},{data:"status",render:e=>e==="deleted"?`<span class="badge badge-light-danger">${r.statuses?.deleted||"Archived"}</span>`:`<span class="badge badge-light-success">${r.statuses?.active||"Active"}</span>`,className:"text-center align-middle"},{data:null,orderable:!1,className:"text-center align-middle",render:e=>D(e)}]});function D(e){const t=[];return t.push(`
            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary me-1 view-user" data-id="${e.id}" title="View">
                ${h("ki-eye")}
            </button>
        `),r.can?.update&&e.status!=="deleted"&&t.push(`
                <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary me-1 edit-user" data-id="${e.id}" title="Edit">
                    ${h("ki-pencil")}
                </button>
            `),r.can?.delete&&(e.status==="deleted"?t.push(`
                    <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-success restore-user" data-id="${e.id}" title="Restore">
                        ${h("ki-arrow-up")}
                    </button>
                `):t.push(`
                    <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-danger delete-user" data-id="${e.id}" title="Delete">
                        ${h("ki-trash")}
                    </button>
                `)),`<div class="d-flex justify-content-center flex-shrink-0">${t.join("")}</div>`}$("#user-search").on("keyup",function(){u.search(this.value).draw()}),E?.addEventListener("click",()=>{u.ajax.reload(),bootstrap.Offcanvas.getInstance(document.getElementById("userFiltersCanvas"))?.hide()}),S?.addEventListener("click",()=>{b?.reset(),u.ajax.reload()}),$("#select-all-users").on("change",function(){$(".user-select").prop("checked",$(this).is(":checked"))}),$("#users-table").on("change",".user-select",function(){$(this).is(":checked")||$("#select-all-users").prop("checked",!1)}),$("#user-form").on("submit",function(e){e.preventDefault();const t=e.currentTarget,s=new FormData(t),a=s.get("user_id"),n=!!a,l=n?o.update.replace("__id__",a):o.store,c="post";n&&s.append("_method","PUT"),y(t);const i=t.querySelector("[data-submit-btn]");A(i,!0),m({method:c,url:l,data:s,headers:{"Content-Type":"multipart/form-data"}}).then(()=>{$("#userModal").modal("hide"),t.reset(),u.ajax.reload(null,!1);const d=n?r.messages?.updated:r.messages?.created;showSuccess(d||"Success")}).catch(d=>T(t,d)).finally(()=>A(i,!1))}),$("#users-table").on("click",".edit-user",function(){const e=$(this).data("id");m.get(o.show.replace("__id__",e)).then(({data:t})=>{const s=document.getElementById("user-form");s.reset(),y(s),$('[name="user_id"]').val(t.user.id),$('[name="name"]').val(t.user.name),$('[name="email"]').val(t.user.email),$('[name="mobile"]').val(t.user.mobile),$('[name="birthdate"]').val(t.user.birthdate),$('[name="gender"]').val(t.user.gender),$("#user-role-select").val(t.user.roles).trigger("change"),$(".password-field").hide(),p.val(0),f(t.user.avatar_url),$("[data-password-label]").text(r.labels?.passwordEdit||"New password"),$("[data-modal-title]").text(r.labels?.edit||"Edit User"),$("#userModal").modal("show")})}),$("#userModal").on("hidden.bs.modal",()=>{const e=document.getElementById("user-form");e.reset(),y(e),$('[name="user_id"]').val(""),p.val(0),f(null),$("[data-password-label]").text(r.labels?.password||"Password"),$("[data-modal-title]").text(r.labels?.create||"Create User"),$(".password-field").show()}),$("#users-table").on("click",".delete-user",function(){const e=$(this).data("id");_("delete").then(t=>{t&&m.delete(o.delete.replace("__id__",e)).then(()=>{u.ajax.reload(null,!1),showSuccess(r.messages?.deleted||"Deleted")})})}),$("#users-table").on("click",".restore-user",function(){const e=$(this).data("id");m.post(o.restore.replace("__id__",e)).then(()=>{u.ajax.reload(null,!1),showSuccess(r.messages?.restored||"Restored")})}),$("#users-table").on("click",".view-user",function(){const e=$(this).data("id");m.get(o.show.replace("__id__",e)).then(({data:t})=>{const s=t.user,a=r.view||{},n=a.empty||"—",l=s.status==="deleted"?`<span class="badge badge-light-danger">${s.status_label??r.statuses?.deleted??"Archived"}</span>`:`<span class="badge badge-light-success">${s.status_label??r.statuses?.active??"Active"}</span>`,c=s.gender_label||(s.gender?a[`gender_${s.gender}`]:null)||n,i=(k,I,j,L=!1)=>`
                    <div class="d-flex align-items-center mb-4">
                        <span class="symbol symbol-35px symbol-circle bg-light-primary me-3">
                            ${h(k)}
                        </span>
                        <div>
                            <div class="text-muted fw-semibold fs-8 text-uppercase">${I}</div>
                            <div class="fw-bold text-gray-900">${L?j:j??n}</div>
                        </div>
                    </div>
                `,d=[i("ki-sms",a.email,s.email??n),i("ki-call",a.mobile,s.mobile??n),i("ki-calendar-8",a.birthdate,w(s.birthdate)),i("ki-user",a.gender,c)].join(""),F=[i("ki-calendar-edit",a.created_at,w(s.created_at)),i("ki-refresh-left",a.updated_at,w(s.updated_at)),i("ki-shield-tick",a.status,l,!0)].join(""),U=Array.isArray(s.roles)&&s.roles.length?s.roles.map(k=>`<span class="badge badge-light-primary me-2 mb-2 px-3 py-2">${k}</span>`).join(""):`<span class="text-muted">${n}</span>`;$("#user-view-body").html(`
                    <div class="card border-0 shadow-sm mb-6">
                        <div class="card-body text-center py-6">
                            <div class="symbol symbol-90px symbol-circle mb-4">
                                <img src="${s.avatar_url??g}" alt="${s.name}">
                            </div>
                            <h3 class="fw-bold mb-1">${s.name}</h3>
                            <div class="text-muted mb-4">${s.email??n}</div>
                            <div class="d-flex flex-wrap justify-content-center gap-4">
                                <div class="border border-dashed rounded py-3 px-5 text-start">
                                    <span class="text-muted text-uppercase fs-8 d-block mb-1">${a.status_hint}</span>
                                    <div class="fw-bold fs-5">${l}</div>
                                </div>
                                <div class="border border-dashed rounded py-3 px-5 text-start">
                                    <span class="text-muted text-uppercase fs-8 d-block mb-1">${a.roles_hint}</span>
                                    <div class="fw-bold fs-5">${s.roles_count??(Array.isArray(s.roles)?s.roles.length:0)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-5">
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0">${a.section_contact}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    ${d}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0">${a.section_meta}</h5>
                                </div>
                                <div class="card-body pt-0">
                                    ${F}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0">${a.section_roles}</h5>
                                </div>
                                <div class="card-body">
                                    ${U}
                                </div>
                            </div>
                        </div>
                    </div>
                `),$("#userViewModal").modal("show")})}),$("#bulk-delete-btn").on("click",()=>{const e=$(".user-select:checked").map((t,s)=>$(s).val()).get();e.length&&_("bulk",e.length).then(t=>{t&&m.post(o.bulkDelete,{ids:e}).then(()=>{u.ajax.reload(null,!1),showSuccess(r.messages?.bulkDeleted||"Deleted")})})}),$('[name="avatar"]').on("change",function(){const e=this.files?.[0];if(!e){f(null);return}const t=new FileReader;t.onload=s=>f(s.target?.result),t.readAsDataURL(e),p.val(0)}),$('[data-kt-image-input-action="remove"]').on("click",()=>{f(null),p.val(1)});function M(){return b?{role:b.role?.value||"",status:b.status?.value||"",date_from:b.date_from?.value||"",date_to:b.date_to?.value||""}:{}}function f(e){C.css("background-image",`url('${e||g}')`)}function y(e){$(e).find(".is-invalid").removeClass("is-invalid"),$(e).find("[data-error-for]").text("")}function T(e,t){if(t.response?.status===422){const s=t.response.data.errors||{};Object.entries(s).forEach(([a,n])=>{const l=a.replace(/\.\d+$/,""),c=$(e).find(`[name="${l}"]`).length?`[name="${l}"]`:`[name="${l}[]"]`;$(e).find(c).addClass("is-invalid");const d=N(l,n[0]);$(e).find(`[data-error-for="${l}"]`).text(d)})}else showError("Something went wrong.")}function h(e){return`<i class="ki-outline ${e} fs-2"></i>`}function N(e,t){const s=r.validation?.[e];if(!s)return t;const a=(t||"").toLowerCase();for(const[n,l]of Object.entries(s)){const c=B[n];if(!c)continue;if(c.some(d=>a.includes(d)))return l}return s.default??t}function w(e){if(!e)return r.view?.empty||"—";try{const t=new Date(e);return Number.isNaN(t.getTime())?e:new Intl.DateTimeFormat(r.locale||"en",{year:"numeric",month:"short",day:"2-digit"}).format(t)}catch{return e}}function _(e,t=1){const s=r.confirm||{},a=e==="bulk"?s.bulkTitle:s.deleteTitle;let n=e==="bulk"?s.bulkMessage:s.deleteMessage;return e==="bulk"&&(n=n?.replace(":count",t)),Swal.fire({title:a,text:n,icon:"warning",showCancelButton:!0,confirmButtonText:s.confirm||"Yes",cancelButtonText:s.cancel||"Cancel",customClass:{confirmButton:"btn btn-danger",cancelButton:"btn btn-light"},buttonsStyling:!1}).then(l=>l.isConfirmed)}function A(e,t){e&&(t?(e.setAttribute("data-kt-indicator","on"),e.disabled=!0):(e.setAttribute("data-kt-indicator","off"),e.disabled=!1))}});
