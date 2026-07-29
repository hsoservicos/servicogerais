const PDFDocument = require('pdfkit');

function generateProposalPDF(proposal) {
  return new Promise((resolve, reject) => {
    const doc = new PDFDocument({
      size: 'A4',
      margin: 50,
      info: {
        Title: `Proposta ${proposal.number}`,
        Author: 'ServiceSaaS',
        Subject: 'Proposta Comercial',
      },
    });

    const buffers = [];
    doc.on('data', chunk => buffers.push(chunk));
    doc.on('end', () => resolve(Buffer.concat(buffers)));
    doc.on('error', reject);

    const { tenant, items } = proposal;
    const pageWidth = doc.page.width - doc.page.margins.left - doc.page.margins.right;
    const primary = '#2563EB';

    doc.font('Helvetica-Bold').fontSize(20).fillColor(primary).text(proposal.number || 'PROPOSTA', { continued: false });
    doc.moveDown(0.3);
    doc.font('Helvetica').fontSize(11).fillColor('#374151').text(proposal.title || '');

    doc.moveDown(1.5);

    const infoY = doc.y;
    const colWidth = pageWidth / 2 - 10;

    if (tenant) {
      doc.font('Helvetica-Bold').fontSize(8).fillColor('#9CA3AF').text('PRESTADOR', 50, infoY);
      doc.moveDown(0.3);
      doc.font('Helvetica').fontSize(10).fillColor('#374151');
      doc.text(tenant.name || '');
      if (tenant.whatsapp) {
        doc.text(`WhatsApp: ${tenant.whatsapp}`);
      }
    }

    const clientX = 50 + colWidth + 20;
    if (proposal.clientName) {
      doc.font('Helvetica-Bold').fontSize(8).fillColor('#9CA3AF').text('CLIENTE', clientX, infoY);
      doc.moveDown(0.3);
      doc.font('Helvetica').fontSize(10).fillColor('#374151');
      doc.text(proposal.clientName, clientX, doc.y);
    }

    doc.moveDown(3);

    const detailsY = doc.y;
    let detailX = 50;
    if (proposal.validUntil) {
      const validDate = new Date(proposal.validUntil).toLocaleDateString('pt-BR');
      doc.font('Helvetica-Bold').fontSize(8).fillColor('#9CA3AF').text('VÁLIDA ATÉ', detailX, detailsY);
      doc.font('Helvetica').fontSize(10).fillColor('#374151').text(validDate, detailX, doc.y + 11);
      detailX += colWidth / 2;
    }

    doc.moveDown(3);

    if (proposal.description) {
      doc.font('Helvetica-Bold').fontSize(8).fillColor('#9CA3AF').text('DESCRIÇÃO');
      doc.moveDown(0.3);
      doc.font('Helvetica').fontSize(10).fillColor('#374151').text(proposal.description);
      doc.moveDown(1.5);
    }

    const tableTop = doc.y + 10;

    doc.rect(50, tableTop - 6, pageWidth, 18).fill(primary);
    doc.fillColor('#FFFFFF').font('Helvetica-Bold').fontSize(9);
    doc.text('ITEM', 55, tableTop, { width: pageWidth * 0.45 });
    doc.text('QTD', 55 + pageWidth * 0.45, tableTop, { width: pageWidth * 0.15, align: 'center' });
    doc.text('VALOR UNIT.', 55 + pageWidth * 0.6, tableTop, { width: pageWidth * 0.2, align: 'right' });
    doc.text('TOTAL', 55 + pageWidth * 0.8, tableTop, { width: pageWidth * 0.2, align: 'right' });

    let rowY = tableTop + 22;
    let rowNum = 0;

    (items || []).forEach(item => {
      const unitPrice = `R$ ${parseFloat(item.unit_price || item.unitPrice || 0).toFixed(2).replace('.', ',')}`;
      const totalPrice = `R$ ${parseFloat(item.total_price || item.totalPrice || (item.quantity || item.quantity || 0) * (item.unit_price || item.unitPrice || 0)).toFixed(2).replace('.', ',')}`;

      if (rowY > doc.page.height - 80) {
        doc.addPage();
        rowY = 50;
      }

      if (rowNum % 2 === 0) {
        doc.rect(50, rowY - 4, pageWidth, 20).fill('#F9FAFB');
      }

      doc.fillColor('#374151').font('Helvetica').fontSize(9);
      doc.text(item.description || 'Item', 55, rowY, { width: pageWidth * 0.45 });
      doc.text(String(item.quantity || item.quantity || 1), 55 + pageWidth * 0.45, rowY, { width: pageWidth * 0.15, align: 'center' });
      doc.text(unitPrice, 55 + pageWidth * 0.6, rowY, { width: pageWidth * 0.2, align: 'right' });
      doc.text(totalPrice, 55 + pageWidth * 0.8, rowY, { width: pageWidth * 0.2, align: 'right' });

      rowY += 20;
      rowNum++;
    });

    const totalLineY = rowY + 6;
    doc.rect(50, totalLineY - 4, pageWidth, 24).fill('#F3F4F6');
    doc.fillColor('#111827').font('Helvetica-Bold').fontSize(11);
    doc.text('VALOR TOTAL', 55, totalLineY, { width: pageWidth * 0.65 });
    doc.text(`R$ ${parseFloat(proposal.totalAmount || proposal.total_amount || 0).toFixed(2).replace('.', ',')}`, 55 + pageWidth * 0.65, totalLineY, { width: pageWidth * 0.35, align: 'right' });

    doc.moveDown(3);

    if (proposal.paymentTerms) {
      doc.font('Helvetica-Bold').fontSize(8).fillColor('#9CA3AF').text('CONDIÇÕES DE PAGAMENTO');
      doc.moveDown(0.3);
      doc.font('Helvetica').fontSize(10).fillColor('#374151').text(proposal.paymentTerms);
      doc.moveDown(1.5);
    }

    if (proposal.notes) {
      doc.font('Helvetica-Bold').fontSize(8).fillColor('#9CA3AF').text('OBSERVAÇÕES');
      doc.moveDown(0.3);
      doc.font('Helvetica').fontSize(10).fillColor('#374151').text(proposal.notes);
      doc.moveDown(1.5);
    }

    doc.moveDown(2);

    doc.fontSize(8).fillColor('#9CA3AF').text(
      `Proposta gerada em ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })} via ServiceSaaS`,
      50, doc.page.height - 50,
      { align: 'center', width: pageWidth }
    );

    doc.end();
  });
}

module.exports = { generateProposalPDF };
