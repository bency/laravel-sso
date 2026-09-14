// ajax 登入輪詢：201 代表已登入，重新整理頁面帶入 session；204 與錯誤則不動。
fetch('/api/sso/ajax/user-info').then(function (response) {
  if (response.ok && response.status !== 204) {
    window.location.reload();
  }
});
