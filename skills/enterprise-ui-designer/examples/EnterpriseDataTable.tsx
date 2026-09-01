import React, { useState } from 'react';
import { Check, Trash2, ArrowUpDown } from 'lucide-react';

export interface DataItem {
  id: string;
  code: string;
  name: string;
  category: string;
  buyPrice: number;
  sellPrice: number;
  stockQty: number;
  status: 'In Stock' | 'Restock' | 'Out of Stock';
}

interface EnterpriseDataTableProps {
  data: DataItem[];
  onDeleteBatch?: (ids: string[]) => void;
  onDeleteItem?: (id: string) => void;
}

export const EnterpriseDataTable: React.FC<EnterpriseDataTableProps> = ({
  data,
  onDeleteBatch,
  onDeleteItem,
}) => {
  const [selectedIds, setSelectedIds] = useState<string[]>([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(10);
  const [sortAsc, setSortAsc] = useState(true);

  // Currency Formatter
  const formatRupiah = (val: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

  // Sorting
  const sortedData = [...data].sort((a, b) =>
    sortAsc ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name)
  );

  // Pagination
  const totalPages = Math.ceil(sortedData.length / pageSize) || 1;
  const paginatedData = sortedData.slice((currentPage - 1) * pageSize, currentPage * pageSize);

  const toggleSelectAll = () => {
    if (selectedIds.length === paginatedData.length && paginatedData.length > 0) {
      setSelectedIds([]);
    } else {
      setSelectedIds(paginatedData.map((d) => d.id));
    }
  };

  const toggleSelectRow = (id: string) => {
    setSelectedIds((prev) => (prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]));
  };

  return (
    <div className="table-card">
      <table className="data-table">
        <thead>
          <tr>
            <th style={{ width: '40px' }}>
              <div
                className={`custom-checkbox ${selectedIds.length === paginatedData.length && paginatedData.length > 0 ? 'checked' : ''}`}
                onClick={toggleSelectAll}
              >
                {selectedIds.length === paginatedData.length && paginatedData.length > 0 && <Check size={12} />}
              </div>
            </th>
            <th onClick={() => setSortAsc(!sortAsc)} style={{ cursor: 'pointer' }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                <span>Kode & Nama Produk</span>
                <ArrowUpDown size={12} />
              </div>
            </th>
            <th>Kategori</th>
            <th className="text-right">Harga Beli</th>
            <th className="text-right">Harga Jual</th>
            <th className="text-right">Stok Gudang</th>
            <th style={{ textAlign: 'center' }}>Status</th>
            <th style={{ width: '60px', textAlign: 'center' }}>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {paginatedData.map((item) => {
            const isSelected = selectedIds.includes(item.id);
            return (
              <tr key={item.id} className={isSelected ? 'selected' : ''}>
                <td>
                  <div
                    className={`custom-checkbox ${isSelected ? 'checked' : ''}`}
                    onClick={() => toggleSelectRow(item.id)}
                  >
                    {isSelected && <Check size={12} />}
                  </div>
                </td>
                <td>
                  <div style={{ fontWeight: 700, color: '#0F172A' }}>{item.name}</div>
                  <div style={{ fontSize: '11px', color: '#64748B', fontFamily: 'monospace' }}>{item.code}</div>
                </td>
                <td>
                  <span style={{ fontSize: '12px', color: '#475569', fontWeight: 500 }}>{item.category}</span>
                </td>
                <td className="text-right" style={{ fontWeight: 600, color: '#64748B' }}>
                  {formatRupiah(item.buyPrice)}
                </td>
                <td className="text-right" style={{ fontWeight: 700, color: '#059669' }}>
                  {formatRupiah(item.sellPrice)}
                </td>
                <td className="text-right" style={{ fontWeight: 700, color: item.stockQty === 0 ? '#DC2626' : '#0F172A' }}>
                  {item.stockQty} Unit
                </td>
                <td style={{ textAlign: 'center' }}>
                  <span
                    className={`status-pill ${
                      item.status === 'In Stock'
                        ? 'instock'
                        : item.status === 'Out of Stock'
                        ? 'outstock'
                        : 'restock'
                    }`}
                  >
                    {item.status}
                  </span>
                </td>
                <td style={{ textAlign: 'center' }}>
                  <button
                    onClick={() => onDeleteItem && onDeleteItem(item.id)}
                    style={{ border: 'none', background: 'transparent', cursor: 'pointer', color: '#9CA3AF' }}
                    title="Hapus Data"
                  >
                    <Trash2 size={14} />
                  </button>
                </td>
              </tr>
            );
          })}

          {paginatedData.length === 0 && (
            <tr className="empty-row">
              <td colSpan={8}>Belum ada data barang. Silakan tambah data baru.</td>
            </tr>
          )}
        </tbody>
      </table>

      {/* Dynamic Functional Pagination */}
      <div className="pagination-container">
        <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
          <span>Tampil per halaman</span>
          <select
            style={{
              padding: '4px 8px',
              borderRadius: '6px',
              border: '1px solid #E5E7EB',
              fontSize: '12px',
              outline: 'none',
              background: '#FFFFFF',
              cursor: 'pointer',
            }}
            value={pageSize}
            onChange={(e) => {
              setPageSize(Number(e.target.value));
              setCurrentPage(1);
            }}
          >
            <option value={10}>10</option>
            <option value={25}>25</option>
            <option value={50}>50</option>
          </select>
          <span style={{ color: '#94A3B8' }}>
            Menampilkan {((currentPage - 1) * pageSize) + 1}–{Math.min(currentPage * pageSize, sortedData.length)} dari {sortedData.length} data
          </span>
        </div>

        <div className="page-numbers">
          <button className="page-num-btn" onClick={() => setCurrentPage(1)} disabled={currentPage === 1}>«</button>
          <button className="page-num-btn" onClick={() => setCurrentPage(Math.max(1, currentPage - 1))} disabled={currentPage === 1}>‹</button>
          {Array.from({ length: totalPages }, (_, i) => i + 1)
            .filter((p) => p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1)
            .map((p, idx, arr) => (
              <React.Fragment key={p}>
                {idx > 0 && arr[idx - 1] !== p - 1 && <span style={{ margin: '0 4px', color: '#9CA3AF' }}>…</span>}
                <button
                  className={`page-num-btn ${currentPage === p ? 'active' : ''}`}
                  onClick={() => setCurrentPage(p)}
                >
                  {p}
                </button>
              </React.Fragment>
            ))}
          <button className="page-num-btn" onClick={() => setCurrentPage(Math.min(totalPages, currentPage + 1))} disabled={currentPage === totalPages}>›</button>
          <button className="page-num-btn" onClick={() => setCurrentPage(totalPages)} disabled={currentPage === totalPages}>»</button>
        </div>
      </div>
    </div>
  );
};
