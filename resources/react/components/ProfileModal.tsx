import React, { useState, useEffect } from 'react';
import {
  X, Award, MapPin, Phone, Mail, User, CreditCard, ShieldAlert, History,
  Cloud, Database, RefreshCw, FileText, Trash2, ExternalLink, LogOut, CheckCircle2, AlertCircle, Heart
} from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { CustomerInfo, Order, Product } from '../types';
import { User as FirebaseUser } from 'firebase/auth';
import { uploadTextFileToDrive, listVIBEFilesFromDrive, deleteFileFromDrive, DriveFile } from '../lib/drive.ts';

interface ProfileModalProps {
  isOpen: boolean;
  onClose: () => void;
  customerInfo: CustomerInfo;
  onSaveProfile: (info: CustomerInfo) => void;
  pastOrders: Order[];
  wishlist: Product[];
  user: FirebaseUser | null;
  accessToken: string | null;
  onGoogleSignIn: () => Promise<void>;
  onLogout: () => Promise<void>;
}

export default function ProfileModal({
  isOpen,
  onClose,
  customerInfo,
  onSaveProfile,
  pastOrders,
  wishlist,
  user,
  accessToken,
  onGoogleSignIn,
  onLogout,
}: ProfileModalProps) {
  const [isEditing, setIsEditing] = useState(false);
  const [form, setForm] = useState<CustomerInfo>({ ...customerInfo });
  const [driveFiles, setDriveFiles] = useState<DriveFile[]>([]);
  const [loadingDrive, setLoadingDrive] = useState(false);
  const [actionLoading, setActionLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  // Synchronize form when customerInfo changes
  useEffect(() => {
    setForm({ ...customerInfo });
  }, [customerInfo]);

  // Load drive files if user is authenticated with Drive access token
  const loadDriveFiles = async () => {
    if (!accessToken) return;
    setLoadingDrive(true);
    setErrorMessage(null);
    try {
      const files = await listVIBEFilesFromDrive(accessToken);
      setDriveFiles(files);
    } catch (error: any) {
      console.error('Error loading Drive files:', error);
      setErrorMessage('Không thể tải danh sách tệp từ Google Drive.');
    } finally {
      setLoadingDrive(false);
    }
  };

  useEffect(() => {
    if (isOpen && accessToken) {
      loadDriveFiles();
    }
  }, [isOpen, accessToken]);

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    onSaveProfile(form);
    setIsEditing(false);
    showSuccess('Cập nhật thông tin giao hàng thành công!');
  };

  const formatVND = (value: number) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
    }).format(value);
  };

  const showSuccess = (msg: string) => {
    setSuccessMessage(msg);
    setTimeout(() => setSuccessMessage(null), 4000);
  };

  const showError = (msg: string) => {
    setErrorMessage(msg);
    setTimeout(() => setErrorMessage(null), 5000);
  };

  // Google Drive backup functions
  const handleBackupOrders = async () => {
    if (!accessToken) return;
    setActionLoading(true);
    setErrorMessage(null);
    try {
      let report = "========= VIBE FASHION STORE - BÁO CÁO ĐƠN HÀNG =========\n";
      report += `Khách hàng: ${customerInfo.fullName || 'Thành viên VIBE'}\n`;
      report += `Email: ${customerInfo.email}\n`;
      report += `Điện thoại: ${customerInfo.phone}\n`;
      report += `Địa chỉ: ${customerInfo.address}, ${customerInfo.city}\n`;
      report += `Ngày xuất báo cáo: ${new Date().toLocaleString('vi-VN')}\n\n`;

      if (pastOrders.length === 0) {
        report += "Bạn chưa có đơn hàng nào trực tuyến trong phiên hiện tại.\n";
      } else {
        pastOrders.forEach((order, idx) => {
          report += `Đơn hàng #${idx + 1} (Mã: ${order.id})\n`;
          report += `Ngày đặt: ${order.date}\n`;
          report += `Phương thức thanh toán: ${order.paymentMethod}\n`;
          report += `Tổng tiền: ${formatVND(order.totalAmount)}\n`;
          report += "Sản phẩm:\n";
          order.items.forEach(item => {
            report += `  - ${item.product.name} (Size: ${item.selectedSize}) x ${item.quantity} - ${formatVND(item.product.price * item.quantity)}\n`;
          });
          report += "--------------------------------------------------------\n\n";
        });
      }

      const dateStr = new Date().toISOString().slice(0, 10);
      const filename = `VIBE_Orders_Backup_${dateStr}.txt`;
      await uploadTextFileToDrive(accessToken, filename, report);
      
      showSuccess(`Đã sao lưu đơn hàng thành công lên Google Drive với tên "${filename}"!`);
      loadDriveFiles();
    } catch (error: any) {
      console.error(error);
      showError('Sao lưu đơn hàng lên Google Drive thất bại.');
    } finally {
      setActionLoading(false);
    }
  };

  const handleBackupWishlist = async () => {
    if (!accessToken) return;
    setActionLoading(true);
    setErrorMessage(null);
    try {
      let report = "========= VIBE FASHION STORE - DANH SÁCH SẢN PHẨM YÊU THÍCH =========\n";
      report += `Ngày xuất: ${new Date().toLocaleString('vi-VN')}\n\n`;

      if (wishlist.length === 0) {
        report += "Danh sách sản phẩm yêu thích của bạn đang trống.\n";
      } else {
        wishlist.forEach((product, idx) => {
          report += `${idx + 1}. ${product.name}\n`;
          report += `   Giá: ${formatVND(product.price)}\n`;
          report += `   Danh mục: ${product.category.toUpperCase()}\n`;
          report += `   Mô tả: ${product.description}\n`;
          report += "--------------------------------------------------------\n";
        });
      }

      const dateStr = new Date().toISOString().slice(0, 10);
      const filename = `VIBE_Wishlist_Backup_${dateStr}.txt`;
      await uploadTextFileToDrive(accessToken, filename, report);
      
      showSuccess(`Đã lưu danh sách yêu thích thành công lên Google Drive với tên "${filename}"!`);
      loadDriveFiles();
    } catch (error: any) {
      console.error(error);
      showError('Lưu danh sách yêu thích lên Google Drive thất bại.');
    } finally {
      setActionLoading(false);
    }
  };

  const handleDeleteFile = async (fileId: string, filename: string) => {
    if (!accessToken) return;
    
    // Explicit user confirmation dialog as mandated
    const confirmed = window.confirm(
      `Bạn có chắc chắn muốn xóa tệp "${filename}" khỏi Google Drive của bạn không? Hành động này không thể hoàn tác.`
    );
    if (!confirmed) return;

    setActionLoading(true);
    setErrorMessage(null);
    try {
      await deleteFileFromDrive(accessToken, fileId);
      showSuccess(`Đã xóa tệp "${filename}" khỏi Google Drive thành công!`);
      loadDriveFiles();
    } catch (error: any) {
      console.error(error);
      showError('Xóa tệp khỏi Google Drive thất bại.');
    } finally {
      setActionLoading(false);
    }
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          {/* Backdrop */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-black/70 backdrop-blur-xs"
          />

          {/* Modal Container */}
          <motion.div
            id="profile-modal-content"
            initial={{ opacity: 0, scale: 0.95, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 20 }}
            className="relative z-10 w-full max-w-2xl overflow-hidden bg-white rounded-md shadow-2xl flex flex-col max-h-[90vh]"
          >
            {/* Header */}
            <div className="flex items-center justify-between border-b border-neutral-100 px-6 py-4.5 bg-neutral-900 text-white">
              <div className="flex items-center gap-2">
                <User className="h-5 w-5 text-neutral-300" />
                <h2 className="font-display text-sm font-bold tracking-widest uppercase text-neutral-100">
                  Tài khoản thành viên VIBE
                </h2>
              </div>
              <button
                id="close-profile-modal"
                onClick={onClose}
                className="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-800 text-neutral-400 hover:text-white hover:bg-neutral-700 transition"
              >
                <X className="h-4 w-4" />
              </button>
            </div>

            {/* Notification Toasts */}
            {successMessage && (
              <div className="bg-emerald-50 border-b border-emerald-100 text-emerald-800 text-xs px-6 py-3 flex items-center gap-2 font-medium">
                <CheckCircle2 className="h-4 w-4 text-emerald-600 flex-shrink-0" />
                <span>{successMessage}</span>
              </div>
            )}
            {errorMessage && (
              <div className="bg-rose-50 border-b border-rose-100 text-rose-800 text-xs px-6 py-3 flex items-center gap-2 font-medium">
                <AlertCircle className="h-4 w-4 text-rose-600 flex-shrink-0" />
                <span>{errorMessage}</span>
              </div>
            )}

            {/* Scrollable body */}
            <div className="flex-1 overflow-y-auto p-6 space-y-6">
              {/* Luxury Black Card Membership */}
              <div className="relative overflow-hidden bg-gradient-to-br from-neutral-900 via-neutral-950 to-neutral-900 text-white p-6 rounded-lg shadow-xl border border-neutral-800">
                <div className="absolute top-0 right-0 h-40 w-40 bg-radial from-neutral-800/40 to-transparent -mr-10 -mt-10 rounded-full" />
                <div className="flex justify-between items-start">
                  <div>
                    <span className="text-[10px] font-mono tracking-widest text-neutral-400 uppercase font-semibold">
                      Thẻ Thành Viên Đặc Quyền
                    </span>
                    <h3 className="text-lg font-black font-display tracking-widest mt-1">VIBE BLACK CARD</h3>
                  </div>
                  <Award className="h-8 w-8 text-amber-400 fill-current" />
                </div>

                <div className="mt-8 flex justify-between items-end">
                  <div>
                    <p className="text-[10px] font-mono text-neutral-400 uppercase tracking-widest">Họ & Tên thành viên</p>
                    <p className="text-sm font-semibold tracking-wide font-display mt-0.5">
                      {user ? (user.displayName || customerInfo.fullName) : (customerInfo.fullName || 'KHÁCH HÀNG THÂN THIẾT')}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="text-[10px] font-mono text-neutral-400 uppercase tracking-widest">Whenever Point</p>
                    <p className="text-sm font-mono font-bold text-amber-400 mt-0.5">1,250 PTS</p>
                  </div>
                </div>

                <div className="mt-4 pt-4 border-t border-neutral-800 flex justify-between text-[10px] font-mono text-neutral-400 tracking-wider">
                  <span>MEMBER ID: {user ? `#UID-${user.uid.slice(0,6).toUpperCase()}` : '#VIBE-7799'}</span>
                  <span>VALID THRU: 12/28</span>
                </div>
              </div>

              {/* CLOUD SQL & OAUTH DRIVE INTEGRATION */}
              <div className="border border-neutral-200 rounded-lg p-5 bg-neutral-50/50 space-y-4">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-neutral-100 pb-3">
                  <div className="flex items-center gap-2">
                    <Cloud className="h-5 w-5 text-neutral-700" />
                    <div>
                      <h4 className="text-xs font-bold uppercase tracking-wider text-neutral-800">
                        Đồng Bộ Google Workspace & Cloud SQL
                      </h4>
                      <p className="text-[10px] text-neutral-400 font-mono">GOOGLE CLOUD DATABASE INTEGRATION</p>
                    </div>
                  </div>
                  {user && (
                    <button
                      onClick={onLogout}
                      className="text-[11px] font-mono font-semibold text-rose-600 hover:text-rose-800 flex items-center gap-1 self-start sm:self-center bg-white px-2.5 py-1 rounded border border-neutral-200 transition"
                    >
                      <LogOut className="h-3 w-3" />
                      Đăng xuất Google
                    </button>
                  )}
                </div>

                {!user ? (
                  <div className="text-center py-4 space-y-4">
                    <p className="text-xs text-neutral-600 max-w-md mx-auto leading-relaxed">
                      Đăng nhập tài khoản Google của bạn để đồng bộ giỏ hàng, đơn hàng lên <b>Cloud SQL Database</b> và sao lưu trực tiếp hóa đơn hoặc sản phẩm yêu thích lên <b>Google Drive</b>.
                    </p>
                    <button
                      onClick={onGoogleSignIn}
                      className="gsi-material-button mx-auto flex items-center gap-3 bg-white hover:bg-neutral-50 text-neutral-700 font-display text-xs font-semibold px-5 py-2.5 rounded border border-neutral-200 shadow-sm transition"
                    >
                      <div className="gsi-material-button-icon h-4 w-4">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style={{ display: 'block' }}>
                          <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                          <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                          <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                          <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                        </svg>
                      </div>
                      <span className="gsi-material-button-contents font-sans tracking-wide">Đăng nhập bằng Google</span>
                    </button>
                  </div>
                ) : (
                  <div className="space-y-4">
                    {/* Google Account details */}
                    <div className="flex items-center gap-3 bg-white p-3 rounded border border-neutral-100">
                      {user.photoURL ? (
                        <img src={user.photoURL} alt={user.displayName || ''} className="h-10 w-10 rounded-full" referrerPolicy="no-referrer" />
                      ) : (
                        <div className="h-10 w-10 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs uppercase font-display">
                          {user.displayName?.slice(0, 2) || 'US'}
                        </div>
                      )}
                      <div>
                        <p className="text-xs font-bold text-neutral-800">{user.displayName}</p>
                        <p className="text-[11px] text-neutral-500 font-mono">{user.email}</p>
                      </div>
                    </div>

                    {/* DB Sync status */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                      <div className="flex items-center gap-2 bg-emerald-50/50 text-emerald-800 border border-emerald-100 p-2.5 rounded-sm">
                        <Database className="h-4 w-4 text-emerald-600 flex-shrink-0" />
                        <div>
                          <p className="font-bold text-[10px] uppercase tracking-wider font-mono">PostgreSQL Cloud SQL</p>
                          <p className="text-[11px]">Đã đồng bộ thời gian thực</p>
                        </div>
                      </div>
                      <div className="flex items-center gap-2 bg-sky-50/50 text-sky-800 border border-sky-100 p-2.5 rounded-sm">
                        <Cloud className="h-4 w-4 text-sky-600 flex-shrink-0" />
                        <div>
                          <p className="font-bold text-[10px] uppercase tracking-wider font-mono">Google Drive Hub</p>
                          <p className="text-[11px]">Kết nối thành công</p>
                        </div>
                      </div>
                    </div>

                    {/* Action buttons */}
                    <div className="space-y-2 pt-2 border-t border-neutral-100">
                      <p className="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Thao tác dữ liệu Google Drive</p>
                      <div className="flex flex-wrap gap-2">
                        <button
                          onClick={handleBackupOrders}
                          disabled={actionLoading}
                          className="flex-1 min-w-[150px] flex items-center justify-center gap-1.5 px-3 py-2 bg-neutral-900 hover:bg-neutral-800 disabled:opacity-50 text-white text-xs font-medium rounded-sm transition"
                        >
                          <FileText className="h-3.5 w-3.5" />
                          Sao lưu đơn hàng (Drive)
                        </button>
                        <button
                          onClick={handleBackupWishlist}
                          disabled={actionLoading}
                          className="flex-1 min-w-[150px] flex items-center justify-center gap-1.5 px-3 py-2 bg-neutral-900 hover:bg-neutral-800 disabled:opacity-50 text-white text-xs font-medium rounded-sm transition"
                        >
                          <Heart className="h-3.5 w-3.5 fill-current" />
                          Lưu yêu thích (Drive)
                        </button>
                        <button
                          onClick={loadDriveFiles}
                          disabled={loadingDrive}
                          className="p-2 border border-neutral-200 text-neutral-600 hover:text-black rounded-sm transition flex items-center justify-center"
                          title="Làm mới danh sách"
                        >
                          <RefreshCw className={`h-3.5 w-3.5 ${loadingDrive ? 'animate-spin' : ''}`} />
                        </button>
                      </div>
                    </div>

                    {/* Lookbook and backup list */}
                    <div className="space-y-2 pt-2 border-t border-neutral-100">
                      <p className="text-[10px] font-bold uppercase tracking-wider text-neutral-500">
                        Danh sách tệp VIBE trên Drive của bạn ({driveFiles.length})
                      </p>

                      {loadingDrive ? (
                        <div className="flex items-center justify-center py-4 gap-2 text-neutral-400 text-xs font-mono">
                          <RefreshCw className="h-4 w-4 animate-spin" />
                          <span>Đang tải tệp từ Google Drive...</span>
                        </div>
                      ) : driveFiles.length === 0 ? (
                        <p className="text-[11px] text-neutral-400 italic py-3 text-center border border-dashed border-neutral-200 rounded-sm">
                          Không tìm thấy tệp sao lưu VIBE nào trên Drive của bạn. Nhấp nút trên để sao lưu.
                        </p>
                      ) : (
                        <div className="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                          {driveFiles.map(file => (
                            <div key={file.id} className="flex items-center justify-between text-xs p-2 bg-white rounded border border-neutral-100 hover:border-neutral-200 transition">
                              <div className="flex items-center gap-2 overflow-hidden mr-2">
                                <FileText className="h-4 w-4 text-neutral-500 flex-shrink-0" />
                                <div className="overflow-hidden">
                                  <p className="font-medium text-neutral-800 truncate" title={file.name}>{file.name}</p>
                                  <p className="text-[9px] text-neutral-400 font-mono">
                                    {new Date(file.createdTime).toLocaleString('vi-VN')}
                                  </p>
                                </div>
                              </div>
                              <div className="flex items-center gap-1 flex-shrink-0">
                                {file.webViewLink && (
                                  <a
                                    href={file.webViewLink}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="p-1 hover:bg-neutral-100 rounded text-neutral-500 hover:text-black transition"
                                    title="Mở trong Google Drive"
                                  >
                                    <ExternalLink className="h-3.5 w-3.5" />
                                  </a>
                                )}
                                <button
                                  onClick={() => handleDeleteFile(file.id, file.name)}
                                  className="p-1 hover:bg-rose-50 rounded text-neutral-400 hover:text-rose-600 transition"
                                  title="Xóa tệp"
                                >
                                  <Trash2 className="h-3.5 w-3.5" />
                                </button>
                              </div>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  </div>
                )}
              </div>

              {/* Editing Form vs details list */}
              {isEditing ? (
                <form id="profile-edit-form" onSubmit={handleSave} className="space-y-4 pt-2">
                  <h4 className="text-xs font-bold uppercase tracking-wider text-neutral-800">Chỉnh sửa thông tin giao hàng</h4>
                  
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Họ và tên</label>
                      <input
                        type="text"
                        required
                        value={form.fullName}
                        onChange={(e) => setForm({ ...form, fullName: e.target.value })}
                        className="mt-1 w-full border border-neutral-200 bg-white px-3 py-2 text-xs rounded-sm focus:border-black focus:outline-none"
                      />
                    </div>
                    <div>
                      <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Số điện thoại</label>
                      <input
                        type="tel"
                        required
                        value={form.phone}
                        onChange={(e) => setForm({ ...form, phone: e.target.value })}
                        className="mt-1 w-full border border-neutral-200 bg-white px-3 py-2 text-xs rounded-sm focus:border-black focus:outline-none"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Email liên hệ</label>
                      <input
                        type="email"
                        required
                        value={form.email}
                        onChange={(e) => setForm({ ...form, email: e.target.value })}
                        className="mt-1 w-full border border-neutral-200 bg-white px-3 py-2 text-xs rounded-sm focus:border-black focus:outline-none"
                      />
                    </div>
                    <div>
                      <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Tỉnh / Thành phố</label>
                      <input
                        type="text"
                        required
                        value={form.city}
                        onChange={(e) => setForm({ ...form, city: e.target.value })}
                        className="mt-1 w-full border border-neutral-200 bg-white px-3 py-2 text-xs rounded-sm focus:border-black focus:outline-none"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-[11px] font-bold text-neutral-600 uppercase tracking-wider">Địa chỉ giao hàng chính xác</label>
                    <input
                      type="text"
                      required
                      value={form.address}
                      onChange={(e) => setForm({ ...form, address: e.target.value })}
                      className="mt-1 w-full border border-neutral-200 bg-white px-3 py-2 text-xs rounded-sm focus:border-black focus:outline-none"
                      placeholder="Số nhà, tên đường, phường/xã, quận/huyện"
                    />
                  </div>

                  <div className="flex gap-2.5 justify-end pt-3">
                    <button
                      id="cancel-profile-edit"
                      type="button"
                      onClick={() => setIsEditing(false)}
                      className="px-4 py-2 text-xs font-semibold uppercase tracking-wider border border-neutral-200 text-neutral-500 hover:text-black hover:border-black rounded-sm"
                    >
                      Hủy bỏ
                    </button>
                    <button
                      id="save-profile-edit"
                      type="submit"
                      className="px-5 py-2 text-xs font-semibold uppercase tracking-wider bg-black text-white hover:bg-neutral-800 rounded-sm"
                    >
                      Lưu thay đổi
                    </button>
                  </div>
                </form>
              ) : (
                <div className="space-y-4">
                  <div className="flex justify-between items-center">
                    <h4 className="text-xs font-bold uppercase tracking-wider text-neutral-800">Thông tin giao hàng mặc định</h4>
                    <button
                      id="edit-profile-btn"
                      onClick={() => setIsEditing(true)}
                      className="text-xs text-neutral-600 font-semibold hover:text-black underline"
                    >
                      Cập nhật thông tin
                    </button>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-sm border border-neutral-100 bg-neutral-50 p-4 text-xs space-y-2 sm:space-y-0">
                    <div className="space-y-2">
                      <p className="flex items-center gap-2 text-neutral-600">
                        <User className="h-4 w-4 text-neutral-800" />
                        <span>Họ tên: <b className="text-black">{customerInfo.fullName || 'Chưa thiết lập'}</b></span>
                      </p>
                      <p className="flex items-center gap-2 text-neutral-600">
                        <Mail className="h-4 w-4 text-neutral-800" />
                        <span>Email: <b className="text-black">{customerInfo.email || 'Chưa thiết lập'}</b></span>
                      </p>
                    </div>
                    <div className="space-y-2">
                      <p className="flex items-center gap-2 text-neutral-600">
                        <Phone className="h-4 w-4 text-neutral-800" />
                        <span>Điện thoại: <b className="text-black">{customerInfo.phone || 'Chưa thiết lập'}</b></span>
                      </p>
                      <p className="flex items-center gap-2 text-neutral-600">
                        <MapPin className="h-4 w-4 text-neutral-800" />
                        <span className="line-clamp-1">Địa chỉ: <b className="text-black">{customerInfo.address ? `${customerInfo.address}, ${customerInfo.city}` : 'Chưa thiết lập'}</b></span>
                      </p>
                    </div>
                  </div>
                </div>
              )}

              {/* Order History */}
              <div className="space-y-3.5 pt-2">
                <h4 className="text-xs font-bold uppercase tracking-wider text-neutral-800 flex items-center gap-1.5">
                  <History className="h-4 w-4 text-neutral-800" />
                  Lịch sử mua sắm trực tuyến ({pastOrders.length})
                </h4>

                {pastOrders.length === 0 ? (
                  <p className="text-xs text-neutral-400 italic text-center py-6 border border-dashed border-neutral-200 rounded-sm">
                    Bạn chưa hoàn tất đơn hàng trực tuyến nào trong phiên hoạt động hiện tại.
                  </p>
                ) : (
                  <div className="space-y-3 max-h-48 overflow-y-auto pr-1">
                    {pastOrders.map((order) => (
                      <div id={`profile-order-item-${order.id}`} key={order.id} className="text-xs border border-neutral-100 rounded-sm bg-neutral-50/55 p-3 flex flex-col gap-2">
                        <div className="flex justify-between items-center border-b border-neutral-100 pb-2">
                          <span className="font-mono font-bold text-neutral-800">Mã đơn: {order.id}</span>
                          <span className="text-[10px] text-neutral-400 font-mono">{order.date}</span>
                        </div>
                        <div className="flex justify-between items-end">
                          <div className="space-y-1">
                            {order.items.map((item, idx) => (
                              <p key={idx} className="text-[11px] text-neutral-600">
                                {item.product.name} (Size: {item.selectedSize}) x <b className="font-mono text-black">{item.quantity}</b>
                              </p>
                            ))}
                          </div>
                          <div className="text-right">
                            <span className="block text-[10px] text-neutral-400 uppercase">Thanh toán {order.paymentMethod}</span>
                            <span className="font-mono font-bold text-black">{formatVND(order.totalAmount)}</span>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>

            {/* Footer info safety */}
            <div className="border-t border-neutral-100 bg-neutral-50 px-6 py-4 flex items-center gap-2 text-[10px] text-neutral-400 font-mono">
              <CreditCard className="h-4 w-4 text-neutral-600" />
              <span>DỮ LIỆU ĐƯỢC MÃ HÓA ĐẦU CUỐI & BẢO MẬT BỞI HỆ THỐNG VIBE PASS & CLOUD SQL.</span>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
}
